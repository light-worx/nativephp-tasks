<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Lightworx\TasksApiClient\TasksApiClient;
use Lightworx\TasksApiClient\DTO\TaskData;

class TaskController extends Controller
{
    public function __construct(
        private readonly TasksApiClient $client
    ) {}

    // -------------------------------------------------------------------------
    // Fetch statuses from the API (cached for 1 hour)
    // Returns a keyed array: ['pending' => ['label' => 'Pending', 'color' => '#f59e0b'], ...]
    // -------------------------------------------------------------------------

    private function statusMap(): array
    {
        try {
            return Cache::remember('tasks_ui.status_map', 3600, function () {
                $statuses = $this->client->meta()->statuses();
                return collect($statuses)->keyBy('label')->toArray();
            });
        } catch (\Throwable $e) {
            return [];
        }
    }
    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    public function index(Request $request): View|RedirectResponse
    {
        if (empty(config('tasks-api.client_id')) || empty(config('tasks-api.client_secret'))) {
            session()->flash('error', 'Please configure your API credentials.');
            return redirect()->route('settings');
        }
        $filter   = $request->get('filter', 'all');
        $page     = (int) $request->get('page', 1);
        $statuses = $this->statusMap();

        try {
            $query = $this->client->tasks();

            if ($filter !== 'all' && isset($statuses[$filter])) {
                $query->whereStatus($filter);
            }

            $response = $query->latest()->paginate(20);
            $tasks    = $response['data'] ?? [];
            $meta     = $response['meta'] ?? [];
            $total    = $meta['total']     ?? count($tasks);
            $lastPage = $meta['last_page'] ?? 1;
        } catch (\Throwable $e) {
            $tasks    = [];
            $total    = 0;
            $lastPage = 1;
            session()->flash('error', 'Could not load tasks: ' . $e->getMessage());
        }
        return view('tasks.index', compact('tasks', 'total', 'filter', 'page', 'lastPage', 'statuses'));
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
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'assigned_email' => 'nullable|email',
            'status'         => 'nullable|string',
            'project_id'     => 'nullable|string',
            'due_at'         => 'nullable|date',
        ]);

        try {
            $this->client->tasks()->create($validated);
            session()->flash('success', 'Task created!');
        } catch (\Throwable $e) {
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
            $raw  = $this->client->http()->get("/api/tasks/{$id}")->json();
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
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'assigned_email' => 'nullable|email',
            'status'         => 'nullable|string',
            'project_id'     => 'nullable|string',
            'due_at'         => 'nullable|date',
        ]);

        try {
            $this->client->tasks()->update($id, $validated);
            session()->flash('success', 'Task updated!');
        } catch (\Throwable $e) {
            session()->flash('error', 'Failed to update task: ' . $e->getMessage());
            return back()->withInput();
        }

        return redirect()->route('tasks.index');
    }

    // -------------------------------------------------------------------------
    // Toggle status — cycles to next status in sort_order
    // -------------------------------------------------------------------------

    public function toggle(string $id)
    {
        try {
            $raw      = $this->client->http()->get("/api/tasks/{$id}")->json();
            $task     = TaskData::fromArray($raw);
            $statuses = $this->statusMap();

            // Build an ordered list of status IDs by sort_order
            $ordered = collect($statuses)
                ->sortBy(fn($s) => $s['sort_order'] ?? 0)
                ->keys()
                ->values();

            $currentIndex = $ordered->search($task->status);
            $nextStatus   = $ordered->get(
                $currentIndex === false ? 0 : ($currentIndex + 1) % $ordered->count()
            );

            $this->client->tasks()->update($id, ['status' => $nextStatus]);
        } catch (\Throwable $e) {
            session()->flash('error', 'Could not update task: ' . $e->getMessage());
        }

        return back();
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    public function destroy(string $id)
    {
        try {
            $this->client->tasks()->delete($id);
            session()->flash('success', 'Task deleted.');
        } catch (\Throwable $e) {
            session()->flash('error', 'Failed to delete task: ' . $e->getMessage());
        }

        return redirect()->route('tasks.index');
    }
}