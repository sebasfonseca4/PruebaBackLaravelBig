<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    
    Route::apiResource('projects', ProjectController::class);
    Route::middleware(['can:create,App\Models\Project'])->group(function () {
        Route::post('projects', [ProjectController::class, 'store']);
    });

    Route::apiResource('tasks', TaskController::class);

    Route::get('tasks/user/{id}', [TaskController::class, 'getTasksForUser']);
    Route::get('/user', [UserController::class, 'getUserRole']);
});