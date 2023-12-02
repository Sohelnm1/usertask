<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Usercontroller;
use App\Http\Controllers\Taskcontroller;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/register', [Usercontroller::class, "register"]);

Route::post('/login', [Usercontroller::class, "login"]);

Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/creatertask', [Taskcontroller::class, 'newtask']);
    Route::get('/tasks', [Taskcontroller::class, 'gettask']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);
    Route::patch('/tasks/{id}/update-status', [TaskController::class, 'updateStatus']);

});