<?php

namespace App\Http\Repositories\TaskRepository;

use App\Models\Task;

class TaskRepository implements TaskInterface {
    private $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function query(): ?object
    {
        return $this->task->query();
    }

    public function index(): ?object
    {
        return $this->task->all();
    }

    public function create(array $data): object
    {
        return $this->task->create($data);
    }

    public function detail(string $id): ?object
    {
        return $this->task->find($id);
    }

    public function update(string $id, array $data): object
    {
        $task = $this->task->findOrFail($id);
        $task->update($data);
        return $task;
    }

    public function delete(string $id): object
    {
        $task = $this->task->findOrFail($id);
        $task->delete();
        return $task;
    }
}

