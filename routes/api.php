<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/register', [App\Http\Controllers\AuthController::class, 'register'])->name('register');
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [App\Http\Controllers\AuthController::class, 'me'])->name('me');
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'getStatistics'])->name('dashboard.statistics');

    Route::post('/ticket', [App\Http\Controllers\TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets', [App\Http\Controllers\TicketController::class, 'index'])->name('tickets.index');
    Route::get('/ticket/{code}', [App\Http\Controllers\TicketController::class, 'show'])->name('tickets.show');
    Route::delete('/ticket/{code}', [App\Http\Controllers\TicketController::class, 'destroy'])->name('tickets.destroy');

    Route::post('/ticket/{code}/reply', [App\Http\Controllers\TicketController::class, 'reply'])->name('tickets.reply');
});
