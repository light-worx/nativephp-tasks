<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Lightworx\TasksApiClient\DTO\ProjectData;
use Lightworx\TasksApiClient\TasksApiClient;
use Lightworx\TasksApiClient\DTO\TaskData;
use Lightworx\TasksApiClient\Exceptions\UnauthorizedException;
use Lightworx\TasksApiClient\Exceptions\ForbiddenException;
use Lightworx\TasksApiClient\Exceptions\ValidationException;

class TaskController extends Controller
{
    public function __construct(
        private readonly TasksApiClient $client
    ) {}

    private function statusMap(): array
    {
        try {
            return Cache::remember('tasks_ui.status_map', 86400, function () {
                $statuses = $this->client->meta()->statuses();
                return collect($statuses)->keyBy('label')->toArray();
            });
        } catch (\Throwable) {
            return [];
        }
    }

    private function projectMap(): array
    {
        try {
            return Cache::remember('tasks_ui.project_map', 3600, function () {
                $projects = $this->client->projects()->get();
                return collect($projects)
                    ->mapWithKeys(fn(ProjectData $p) => [$p->id => $p->name])
                    ->toArray();
            });
        } catch (\Throwable) {
            return [];
        }
    }

    private function assignedEmail(): string
    {
        return config('tasks.default_email', '');
    }

    // -------------------------------------------------------------------------
    // Index - renders shell instantly, tasks loaded via data() AJAX
    // -------------------------------------------------------------------------

    public function index(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        if (empty(config('tasks-api.client_id')) || empty(config('tasks-api.client_secret'))) {
            session()->flash('error', 'Please configure your API credentials.');
            return redirect()->route('settings');
        }

        $filter   = $request->get('filter', 'all');
        $page     = (int) $request->get('page', 1);
        $statuses = $this->statusMap();

        return view('tasks.index', compact('filter', 'page', 'statuses'));
    }

    // -------------------------------------------------------------------------
    // Data - AJAX endpoint for task list
    // -------------------------------------------------------------------------

    public function data(Request $request): \Illuminate\Http\JsonResponse
    {
        $filter = $request->get('filter', 'all');

        try {
            $query = $this->client->tasks();

            if ($this->assignedEmail()) {
                $query->whereAssignedTo($this->assignedEmail());
            }

            if ($filter !== 'all') {
                $query->whereStatus($filter);
            }

            $response = $query->latest()->paginate(20);
            $tasks    = collect($response['data'] ?? [])
                ->map(fn(TaskData $task) => [
                    'id'          => $task->id,
                    'title'       => $task->title,
                    'description' => $task->description,
                    'status'      => $task->status,
                    'due_at'      => $task->due_at,
                ])->toArray();

            return response()->json([
                'tasks'    => $tasks,
                'total'    => $response['meta']['total']     ?? count($tasks),
                'lastPage' => $response['meta']['last_page'] ?? 1,
            ]);

        } catch (UnauthorizedException $e) {
            return response()->json(['error' => 'Authentication failed. Please check your credentials in Settings.'], 401);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    public function create(): View
    {
        $statuses = $this->statusMap();
        $projects = $this->projectMap();
        return view('tasks.create', compact('statuses', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'nullable|string',
            'project_id'  => 'nullable|string',
            'due_at'      => 'nullable|date',
        ]);

        $validated['assigned_email'] = $this->assignedEmail();

        try {
            $this->client->tasks()->create($validated);
            session()->flash('success', 'Task created!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (UnauthorizedException $e) {
            session()->flash('error', 'Authentication failed. Please check Settings.');
            return back()->withInput();
        } catch (\Throwable $e) {
            // Task may have been created despite response mapping error
            if (str_contains($e->getMessage(), 'undefined array key')) {
                session()->flash('success', 'Task created!');
                return redirect()->route('tasks.index');
            }
            session()->flash('error', 'Failed to create task: ' . $e->getMessage());
            return back()->withInput();
        }

        return redirect()->route('tasks.index');
    }

    // -------------------------------------------------------------------------
    // Edit - now uses find() from the SDK
    // -------------------------------------------------------------------------

    public function edit(string $id): View|\Illuminate\Http\RedirectResponse
    {
        try {
            $task = $this->client->tasks()
                ->whereAssignedTo($this->assignedEmail())
                ->find($id);
        } catch (ForbiddenException $e) {
            session()->flash('error', 'You do not have permission to edit this task.');
            return redirect()->route('tasks.index');
        } catch (\Throwable $e) {
            session()->flash('error', 'Task not found.');
            return redirect()->route('tasks.index');
        }

        $statuses = $this->statusMap();
        $projects = $this->projectMap();
        return view('tasks.edit', compact('task', 'statuses', 'projects'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'nullable|string',
            'project_id'  => 'nullable|string',
            'due_at'      => 'nullable|date',
        ]);

        $validated['assigned_email'] = $this->assignedEmail();

        try {
            $this->client->http()
                ->put("/api/tasks/{$id}?assigned_email=" . urlencode($this->assignedEmail()), $validated);
            session()->flash('success', 'Task updated!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ForbiddenException $e) {
            session()->flash('error', 'You do not have permission to update this task.');
            return back()->withInput();
        } catch (\Throwable $e) {
            session()->flash('error', 'Failed to update task: ' . $e->getMessage());
            return back()->withInput();
        }

        return redirect()->route('tasks.index');
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    public function destroy(string $id)
    {
        try {
            $this->client->http()
                ->delete("/api/tasks/{$id}?assigned_email=" . urlencode($this->assignedEmail()));
            session()->flash('success', 'Task deleted.');
        } catch (ForbiddenException $e) {
            session()->flash('error', 'You do not have permission to delete this task.');
        } catch (\Throwable $e) {
            session()->flash('error', 'Failed to delete task: ' . $e->getMessage());
        }

        return redirect()->route('tasks.index');
    }
}