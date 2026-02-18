<?php

use App\Modules\Request\Http\Controllers\DispatcherController;
use App\Modules\Request\Http\Controllers\MasterController;
use App\Modules\Request\Http\Controllers\RequestController;
use Illuminate\Support\Facades\Route;

// Публичная страница создания заявки
Route::prefix('requests')->name('requests.')->group(function () {
    Route::get('/create', [RequestController::class, 'create'])->name('create');
    Route::post('/', [RequestController::class, 'store'])->name('store');
});

// Панель диспетчера
Route::middleware(['auth'])->prefix('dispatcher')->name('dispatcher.')->group(function () {
    Route::get('/', [DispatcherController::class, 'index'])->name('index');
    Route::post('/requests/{request}/assign', [DispatcherController::class, 'assign'])->name('requests.assign');
    Route::post('/requests/{request}/cancel', [DispatcherController::class, 'cancel'])->name('requests.cancel');
});

// Панель мастера
Route::middleware(['auth'])->prefix('master')->name('master.')->group(function () {
    Route::get('/', [MasterController::class, 'index'])->name('index');
    Route::post('/requests/{request}/take', [MasterController::class, 'takeInProgress'])->name('requests.take');
    Route::post('/requests/{request}/complete', [MasterController::class, 'complete'])->name('requests.complete');
});
