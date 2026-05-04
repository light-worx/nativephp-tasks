<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Lightworx\TasksApiClient\TasksApiClient;
use Lightworx\TasksApiClient\DTO\TaskData;

class TaskController extends Controller
{
    public function __construct(
        private readonly TasksApiClient $client
    ) {}

    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    public function index(Request $request): View
    {
        $filter = $request->get('filter', 'all'); // all | pending | completed
        $page   = (int) $request->get('page', 1);

        try {
            $query = $this->client->tasks();

            if (in_array($filter, ['pending', 'completed'])) {
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

        return view('tasks.index', compact('tasks', 'total', 'filter', 'page', 'lastPage'));
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    public function create(): View
    {
        return view('tasks.create');
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

    public function edit(string $id): View
    {
        try {
            $raw  = $this->client->http()->get("/api/tasks/{$id}")->json();
            $task = TaskData::fromArray($raw);
        } catch (\Throwable $e) {
            session()->flash('error', 'Task not found.');
            return redirect()->route('tasks.index');
        }

        return view('tasks.edit', compact('task'));
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
    // Toggle status (pending <-> completed)
    // -------------------------------------------------------------------------

    public function toggle(string $id)
    {
        try {
            $raw      = $this->client->http()->get("/api/tasks/{$id}")->json();
            $task     = TaskData::fromArray($raw);
            $newStatus = $task->status === 'completed' ? 'pending' : 'completed';
            $this->client->tasks()->update($id, ['status' => $newStatus]);
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