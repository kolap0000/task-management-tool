<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{ 
    TaskController, 
    StatusController,
    UserController 
}; 

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth:sanctum')->group( function () {
    Route::post('tasks',[TaskController::class, 'store'])->name('tasks.store');
    Route::put('tasks/sync', [TaskController::class, 'sync'])->name('tasks.sync');
    Route::put('tasks/delete', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::put('tasks/update', [TaskController::class, 'update'])->name('tasks.update');
    

    Route::post('statuses', [StatusController::class, 'store'])->name('statuses.store');
    Route::put('statuses', [StatusController::class, 'update'])->name('statuses.update');
});

Route::post('/login',[UserController::class,'login']);