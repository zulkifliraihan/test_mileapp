<?php

namespace App\Http\Services\Task;

use App\Http\Repositories\TaskRepository\TaskInterface;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class TaskService
{
    public function __construct(private TaskInterface $tasks)
    {
    }

    private function resource($task): TaskResource
    {
        return new TaskResource($task);
    }

    public function index(array $params): array
    {
        // filters, sort, pagination
        $query = $this->tasks->query();

        $filters = $params['filter'] ?? [];
        if (isset($filters['status'])) {
            $query->where('status', (string) $filters['status']);
        }
        if (isset($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }

        // Optional: created_at date range
        if (!empty($filters['start_date'])) {
            $query->where('created_at', '>=', Carbon::parse($filters['start_date'])->startOfDay());
        }
        if (!empty($filters['end_date'])) {
            $query->where('created_at', '<=', Carbon::parse($filters['end_date'])->endOfDay());
        }

        $sort = $params['sort'] ?? null;
        if (is_array($sort) && !empty($sort['field']) && !empty($sort['dir'])) {
            $field = $sort['field'];
            $direction = strtolower($sort['dir']);
            $query->orderBy($field, $direction);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = (int) ($params['per_page'] ?? 10);

        $paginator = $query->paginate($perPage);
        $items = TaskResource::collection(collect($paginator->items()));

        $data = [
            'meta' => Arr::except($paginator->toArray(), ['data']),
            'tasks' => $items,
        ];

        return [
            'status' => true,
            'response' => 'get',
            'data' => $data,
        ];
    }

    public function store(array $data): array
    {
        $task = $this->tasks->create($data);

        return [
            'status' => true,
            'response' => 'created',
            'data' => $this->resource($task)->resolve(),
        ];
    }

    public function update(string $id, array $data): array
    {
        $task = $this->tasks->detail($id);
        if (!$task) {
            return [
                'status' => false,
                'response' => 'not-found',
                'errors' => ["Task with the id {$id} was not found"],
            ];
        }

        $task->update($data);

        return [
            'status' => true,
            'response' => 'updated',
            'message' => 'Task updated',
            'data' => $this->resource($task)->resolve(),
        ];
    }

    public function show(string $id): array
    {
        $task = $this->tasks->detail($id);
        if (!$task) {
            return [
                'status' => false,
                'response' => 'not-found',
                'errors' => ["Task with the id {$id} was not found"],
            ];
        }

        return [
            'status' => true,
            'response' => 'get',
            'data' => $this->resource($task)->resolve(),
        ];
    }

    public function destroy(string $id): array
    {
        $task = $this->tasks->detail($id);
        if (!$task) {
            return [
                'status' => false,
                'response' => 'not-found',
                'errors' => ["Task with the id {$id} was not found"],
            ];
        }

        $task->delete();

        return [
            'status' => true,
            'response' => 'deleted',
            'data' => null,
        ];
    }
}
