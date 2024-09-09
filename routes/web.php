<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [TaskController::class, 'view'] );
Route::get('/users', [UserController::class, 'getUsers'] );
Route::get('/user/tasks', [TaskController::class, 'getUserTasks']);
Route::post('/user/task/create', [TaskController::class, 'create']);
Route::post('/user/task/{task}', [TaskController::class, 'update']);
Route::post('/user/tasks/complete', [TaskController::class, 'completeTasks']);
Route::delete('/user/tasks', [TaskController::class, 'delete']);
