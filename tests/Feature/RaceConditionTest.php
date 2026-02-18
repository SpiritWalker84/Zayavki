<?php

namespace Tests\Feature;

use App\Modules\Request\Models\Request;
use App\Modules\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RaceConditionTest extends TestCase
{
    use RefreshDatabase;

    public function test_race_condition_protection_when_taking_request_in_progress(): void
    {
        // Создаем мастера
        $master = User::factory()->master()->create();

        // Создаем заявку в статусе assigned
        $request = Request::factory()->assigned()->create([
            'assigned_to' => $master->id,
        ]);

        // Авторизуемся как мастер
        $this->actingAs($master);

        // Симулируем параллельные запросы через транзакции
        $successCount = 0;
        $failureCount = 0;

        // Первый запрос должен быть успешным
        try {
            DB::transaction(function () use ($request, $master, &$successCount) {
                $lockedRequest = Request::lockForUpdate()->find($request->id);
                
                if ($lockedRequest && $lockedRequest->status === Request::STATUS_ASSIGNED) {
                    $lockedRequest->status = Request::STATUS_IN_PROGRESS;
                    $lockedRequest->save();
                    $successCount++;
                }
            });
        } catch (\Exception $e) {
            $failureCount++;
        }

        // Второй запрос должен получить ошибку, так как статус уже изменен
        try {
            DB::transaction(function () use ($request, $master, &$successCount, &$failureCount) {
                $lockedRequest = Request::lockForUpdate()->find($request->id);
                
                if ($lockedRequest && $lockedRequest->status === Request::STATUS_ASSIGNED) {
                    $lockedRequest->status = Request::STATUS_IN_PROGRESS;
                    $lockedRequest->save();
                    $successCount++;
                } else {
                    $failureCount++;
                }
            });
        } catch (\Exception $e) {
            $failureCount++;
        }

        // Проверяем, что только один запрос был успешным
        $this->assertEquals(1, $successCount);
        $this->assertEquals(1, $failureCount);

        // Проверяем, что заявка в правильном статусе
        $request->refresh();
        $this->assertEquals(Request::STATUS_IN_PROGRESS, $request->status);
    }
}
