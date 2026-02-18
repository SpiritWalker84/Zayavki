<?php

namespace App\Modules\Request\Repositories;

use App\Modules\Request\Models\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface RequestRepositoryInterface
{
    public function find(int $id): ?Request;

    public function findAll(): Collection;

    public function paginate(int $perPage = 15, ?array $filters = null): LengthAwarePaginator;

    public function create(array $data): Request;

    public function update(Request $request, array $data): Request;

    public function delete(Request $request): bool;

    public function findByStatus(string $status): Collection;

    public function findByAssignedTo(int $userId): Collection;
}
