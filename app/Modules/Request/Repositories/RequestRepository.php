<?php

namespace App\Modules\Request\Repositories;

use App\Modules\Request\Models\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

class RequestRepository implements RequestRepositoryInterface
{
    public function find(int $id): ?Request
    {
        return Request::with('assignedTo')->find($id);
    }

    public function findAll(): Collection
    {
        return Request::with('assignedTo')->latest()->get();
    }

    public function paginate(int $perPage = 15, ?array $filters = null): LengthAwarePaginator
    {
        $query = Request::with('assignedTo');

        if ($filters) {
            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }
            if (!empty($filters['assigned_to'])) {
                $query->where('assigned_to', $filters['assigned_to']);
            }
        }

        return $query->latest()->paginate($perPage);
    }

    public function create(array $data): Request
    {
        return Request::create($data);
    }

    public function update(Request $request, array $data): Request
    {
        $request->update($data);
        return $request->fresh(['assignedTo']);
    }

    public function delete(Request $request): bool
    {
        return $request->delete();
    }

    public function findByStatus(string $status): Collection
    {
        return Request::where('status', $status)
            ->with('assignedTo')
            ->latest()
            ->get();
    }

    public function findByAssignedTo(int $userId): Collection
    {
        return Request::where('assigned_to', $userId)
            ->with('assignedTo')
            ->latest()
            ->get();
    }
}
