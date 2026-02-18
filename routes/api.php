<?php

use Illuminate\Support\Facades\Route;

// API можно добавить позже в модулях
Route::middleware('auth:sanctum')->get('/user', function (\Illuminate\Http\Request $request) {
    return $request->user();
});
