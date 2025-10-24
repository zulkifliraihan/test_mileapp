<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\IndexTaskRequest;
use App\Http\Services\Task\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(private TaskService $taskService) {}

    public function index(IndexTaskRequest $request): JsonResponse
    {
        try {
            $service = $this->taskService->index($request->validated());
            if ($service['status']) {
                return $this->success($service['response'], $service['data']);
            }
            return $this->errorServer($service['errors'] ?? ['Unexpected error']);
        } catch (\Throwable $th) {
            return $this->errorServer($th->getMessage());
        }
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        try {
            $service = $this->taskService->store($request->validated());
            if ($service['status']) {
                return $this->success($service['response'], $service['data']);
            }
            return $this->errorServer($service['errors'] ?? ['Unexpected error']);
        } catch (\Throwable $th) {
            return $this->errorServer($th->getMessage());
        }
    }

    public function update(UpdateTaskRequest $request, string $id): JsonResponse
    {
        try {
            $service = $this->taskService->update($id, $request->validated());
            if ($service['status']) {
                return $this->success($service['response'], $service['data']);
            }
            if (($service['response'] ?? null) === 'not-found') {
                return $this->errorNotFound($service['errors'] ?? ['Task not found']);
            }
            return $this->errorServer($service['errors'] ?? ['Unexpected error']);
        } catch (\Throwable $th) {
            return $this->errorServer($th->getMessage());
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $service = $this->taskService->show($id);
            if ($service['status']) {
                return $this->success($service['response'], $service['data']);
            }
            if (($service['response'] ?? null) === 'not-found') {
                return $this->errorNotFound($service['errors'] ?? ['Task not found']);
            }
            return $this->errorServer($service['errors'] ?? ['Unexpected error']);
        } catch (\Throwable $th) {
            return $this->errorServer($th->getMessage());
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $service = $this->taskService->destroy($id);
            if ($service['status']) {
                return $this->success($service['response'], $service['data']);
            }
            if (($service['response'] ?? null) === 'not-found') {
                return $this->errorNotFound($service['errors'] ?? ['Task not found']);
            }
            return $this->errorServer($service['errors'] ?? ['Unexpected error']);
        } catch (\Throwable $th) {
            return $this->errorServer($th->getMessage());
        }
    }
}
