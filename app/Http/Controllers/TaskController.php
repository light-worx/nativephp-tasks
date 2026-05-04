<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Lightworx\TasksApiClient\TasksApiClient;

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
        $filter    = $request->get('filter', 'all');  // all | pending | completed
        $priority  = $request->get('priority');
        $search    = $request->get('search');
        $page      = (int) $request->get('page', 1);

        $params = [
            'page'     => $page,
            'per_page' => 20,
        ];

        if ($filter === 'pending') {
            $params['completed'] = 0;
        } elseif ($filter === 'completed') {
            $params['completed'] = 1;
        }

        if ($priority) {
            $params['priority'] = $priority;
        }

        if ($search) {
            $params['search'] = $search;
        }

        try {
            $response = $this->client->tasks()->paginate($params);
            $tasks    = $response['data']         ?? $response;
            $total    = $response['total']        ?? count($tasks);
            $lastPage = $response['last_page']    ?? 1;
        } catch (\Throwable $e) {
            $tasks    = [];
            $total    = 0;
            $lastPage = 1;
            session()->flash('error', 'Could not load tasks: ' . $e->getMessage());
        }

        return view('tasks.index', compact('tasks', 'total', 'filter', 'priority', 'search', 'page', 'lastPage'));
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
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority'    => 'nullable|in:low,medium,high',
            'category'    => 'nullable|string|max:100',
            'due_date'    => 'nullable|date',
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

    public function edit(int $id): View
    {
        try {
            $task = $this->client->tasks()->find($id);
        } catch (\Throwable $e) {
            session()->flash('error', 'Task not found.');
            return redirect()->route('tasks.index');
        }

        return view('tasks.edit', compact('task'));
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority'    => 'nullable|in:low,medium,high',
            'category'    => 'nullable|string|max:100',
            'due_date'    => 'nullable|date',
            'completed'   => 'nullable|boolean',
        ]);

        // Checkbox sends "1" when checked, nothing when unchecked
        $validated['completed'] = $request->boolean('completed');

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
    // Toggle complete (quick action)
    // -------------------------------------------------------------------------

    public function toggle(int $id)
    {
        try {
            $task = $this->client->tasks()->find($id);
            $this->client->tasks()->update($id, ['completed' => ! $task['completed']]);
        } catch (\Throwable $e) {
            session()->flash('error', 'Could not toggle task: ' . $e->getMessage());
        }

        return back();
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    public function destroy(int $id)
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
