<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{ TaskController, StatusController }; 


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Route::get('tasks', [TaskController::class, 'index'])->name('tasks.index');

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return redirect()->route('tasks.index');
});
Auth::routes();

Route::get('/home', function () {
    return redirect()->route('tasks.index');
})->name('home');

Route::middleware('auth:sanctum')->group( function () {
    Route::get('tasks', [TaskController::class, 'index'])->name('tasks.index');
});



