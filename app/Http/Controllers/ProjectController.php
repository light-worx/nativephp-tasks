<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use Lightworx\TasksApiClient\TasksApiClient;

class ProjectController extends Controller
{
    public function __construct(
        private readonly TasksApiClient $client
    ) {}

    public function index(): View
    {
        try {
            $response = $this->client->http()->get('/api/projects')->json();
            $projects = is_array($response) && isset($response[0])
                ? $response
                : ($response['data'] ?? []);
        } catch (\Throwable $e) {
            $projects = [];
            session()->flash('error', 'Could not load projects: ' . $e->getMessage());
        }

        return view('projects.index', compact('projects'));
    }

    public function tasks(int $id): View
    {
        try {
            // Fetch the project details
            $response = $this->client->http()->get('/api/projects')->json();
            $projects = is_array($response) && isset($response[0])
                ? $response
                : ($response['data'] ?? []);

            $project = collect($projects)->firstWhere('id', $id);

            if (! $project) {
                session()->flash('error', 'Project not found.');
                return redirect()->route('projects.index');
            }

            // Fetch statuses for colour coding
            $statuses = Cache::get('tasks_ui.status_map', []);

        } catch (\Throwable $e) {
            session()->flash('error', 'Could not load project: ' . $e->getMessage());
            return redirect()->route('projects.index');
        }

        return view('projects.tasks', compact('project', 'statuses'));
    }
}