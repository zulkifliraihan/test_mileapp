<?php

namespace App\Http\Repositories\TaskRepository;

interface TaskInterface {
    public function query(): ?object;
    public function index(): ?object;
    public function create(array $data): object;
    public function detail(string $id): ?object;
    public function update(string $id, array $data): object;
    public function delete(string $id): object;
}

