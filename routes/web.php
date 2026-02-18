<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->isDispatcher()) {
        return redirect()->route('dispatcher.index');
    } elseif ($user->isMaster()) {
        return redirect()->route('master.index');
    }
    return redirect()->route('home');
})->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';
