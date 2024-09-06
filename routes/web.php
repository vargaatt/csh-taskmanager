<?php

use App\Http\Controllers\TaskController;
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
Route::get('/user/tasks', [TaskController::class, 'getUserTasks']);
Route::post('/user/tasks/create', [TaskController::class, 'create']);
Route::post('/user/tasks/{task}', [TaskController::class, 'update']);
Route::delete('/user/tasks', [TaskController::class, 'delete']);
