<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Lightworx\TasksApiClient\TasksApiClient;
use Lightworx\TasksApiClient\DTO\TaskData;

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
    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    public function index(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        if (empty(config('tasks-api.client_id')) || empty(config('tasks-api.client_secret'))) {
            session()->flash('error', 'Please configure your API credentials.');
            return redirect()->route('settings');
        }

        $filter   = $request->get('filter', 'all');
        $page     = (int) $request->get('page', 1);
        Log::info('index start', ['time' => microtime(true)]);
        $statuses = $this->statusMap();
        Log::info('statusMap done', ['time' => microtime(true)]);
        return view('tasks.index', compact('filter', 'page', 'statuses'));
    }

    public function data(Request $request): \Illuminate\Http\JsonResponse
    {
        $filter = $request->get('filter', 'all');
        $page   = (int) $request->get('page', 1);

        try {
            $query = $this->client->tasks();

            // Always filter to the configured user's email
            if ($this->assignedEmail()) {
                $query->whereAssignedTo($this->assignedEmail());
            }

            if ($filter !== 'all') {
                $query->whereStatus($filter);
            }

            $response = $query->latest()->paginate(20);
            $tasks    = collect($response['data'] ?? [])
                ->map(fn($task) => [
                    'id'          => $task->id,
                    'title'       => $task->title,
                    'description' => $task->description,
                    'status'      => $task->status,
                    'due_at'      => $task->due_at,
                ])->toArray();
            Log::info('tasks fetched', ['time' => microtime(true)]);
            return response()->json([
                'tasks'    => $tasks,
                'total'    => $response['meta']['total']     ?? count($tasks),
                'lastPage' => $response['meta']['last_page'] ?? 1,
            ]);

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
        return view('tasks.create', compact('statuses'));
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

        $validated['assigned_email'] = config('tasks.default_email', '');

        try {
            $this->client->tasks()->create($validated);
            session()->flash('success', 'Task created!');
        } catch (\Throwable $e) {
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
    // Edit
    // -------------------------------------------------------------------------

    public function edit(string $id): View|RedirectResponse
    {
        try {
            $raw  = $this->client->http()
                ->get("/api/tasks/{$id}", [
                    'assigned_email' => $this->assignedEmail(),
                ])
                ->json();
            $task = TaskData::fromArray($raw);
        } catch (\Throwable $e) {
            session()->flash('error', 'Task not found.');
            return redirect()->route('tasks.index');
        }

        $statuses = $this->statusMap();
        return view('tasks.edit', compact('task', 'statuses'));
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
        } catch (\Throwable $e) {
            session()->flash('error', 'Failed to update task: ' . $e->getMessage());
            return back()->withInput();
        }

        return redirect()->route('tasks.index');
    }

    public function destroy(string $id)
    {
        try {
            $this->client->http()
                ->delete("/api/tasks/{$id}?assigned_email=" . urlencode($this->assignedEmail()));
            session()->flash('success', 'Task deleted.');
        } catch (\Throwable $e) {
            session()->flash('error', 'Failed to delete task: ' . $e->getMessage());
        }

        return redirect()->route('tasks.index');
    }

    private function assignedEmail(): string
    {
        return config('tasks.default_email', '');
    }
}