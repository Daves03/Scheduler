<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TutorController;
use App\Http\Controllers\TutoringSessionController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AuthController; // <--- Don't forget this import!
use App\Http\Controllers\AdminController; // <--- Import this

// --- PUBLIC ROUTES ---
// The only thing public is the ability to Login
Route::post('/login', [AuthController::class, 'login']);

// --- PROTECTED ROUTES ---
// Everything else requires a valid Token
Route::middleware('auth:sanctum')->group(function () {
    
    // Logout route
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/admin/users/{id}/restore', [AdminController::class, 'restore']);
    
    // Manage Data (View, Create, Edit, Delete)
    Route::apiResource('tutors', TutorController::class);
    Route::apiResource('sessions', TutoringSessionController::class);
    Route::apiResource('bookings', BookingController::class);



    Route::get('/admin/stats', [AdminController::class, 'stats']);
    Route::get('/admin/users', [AdminController::class, 'index']);
    Route::put('/admin/users/{id}', [AdminController::class, 'update']);
    Route::delete('/admin/users/{id}', [AdminController::class, 'destroy']);
    

});
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);