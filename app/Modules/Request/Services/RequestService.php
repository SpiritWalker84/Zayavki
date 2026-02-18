<?php

namespace App\Modules\Request\Services;

use App\Modules\Request\Models\Request;
use App\Modules\Request\Repositories\RequestRepositoryInterface;
use App\Modules\User\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RequestService
{
    public function __construct(
        private RequestRepositoryInterface $repository
    ) {}

    public function paginate(int $perPage = 15, ?array $filters = null): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function findById(int $id): ?Request
    {
        return $this->repository->find($id);
    }

    public function create(array $data): Request
    {
        $data['status'] = Request::STATUS_NEW;
        return $this->repository->create($data);
    }

    public function update(Request $request, array $data): Request
    {
        return $this->repository->update($request, $data);
    }

    public function assign(Request $request, User $master): Request
    {
        return $this->repository->update($request, [
            'assigned_to' => $master->id,
            'status' => Request::STATUS_ASSIGNED,
        ]);
    }

    public function cancel(Request $request): Request
    {
        return $this->repository->update($request, [
            'status' => Request::STATUS_CANCELED,
        ]);
    }

    /**
     * Безопасное взятие заявки в работу с защитой от race condition
     * Использует транзакцию и блокировку строки для предотвращения параллельного доступа
     */
    public function takeInProgress(Request $request, User $master): Request
    {
        return DB::transaction(function () use ($request, $master) {
            // Блокируем строку для чтения и обновления
            $lockedRequest = Request::lockForUpdate()->find($request->id);
            
            if (!$lockedRequest) {
                throw new \RuntimeException('Заявка не найдена');
            }

            // Проверяем, что заявка назначена на этого мастера
            if ($lockedRequest->assigned_to !== $master->id) {
                throw new \RuntimeException('Заявка не назначена на этого мастера');
            }

            // Проверяем, что заявка в статусе assigned
            if ($lockedRequest->status !== Request::STATUS_ASSIGNED) {
                $statusMessages = [
                    Request::STATUS_IN_PROGRESS => 'Заявка уже взята в работу',
                    Request::STATUS_DONE => 'Заявка уже завершена',
                    Request::STATUS_CANCELED => 'Заявка отменена',
                    Request::STATUS_NEW => 'Заявка еще не назначена мастеру',
                ];
                
                $message = $statusMessages[$lockedRequest->status] 
                    ?? "Заявка уже в статусе: {$lockedRequest->status}";
                    
                throw new \RuntimeException($message);
            }

            // Обновляем статус
            $lockedRequest->status = Request::STATUS_IN_PROGRESS;
            $lockedRequest->save();

            Log::info("Заявка #{$lockedRequest->id} взята в работу мастером #{$master->id}");

            return $lockedRequest->fresh(['assignedTo']);
        });
    }

    public function complete(Request $request, User $master): Request
    {
        if ($request->assigned_to !== $master->id) {
            throw new \RuntimeException('Заявка не назначена на этого мастера');
        }

        if (!$request->canBeCompleted()) {
            throw new \RuntimeException('Заявка не может быть завершена в текущем статусе');
        }

        return $this->repository->update($request, [
            'status' => Request::STATUS_DONE,
        ]);
    }

    public function getByStatus(string $status): Collection
    {
        return $this->repository->findByStatus($status);
    }

    public function getByAssignedTo(User $master): Collection
    {
        return $this->repository->findByAssignedTo($master->id);
    }
}
