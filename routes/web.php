<?php

use App\Http\Controllers\ChatRoomController;
use App\Http\Controllers\SendMessageController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('{room?}', ChatRoomController::class)->name('dashboard');
   
});
Route::get('loading/test', function(){
    return Inertia::render("Loading");
});
Route::middleware(['auth', 'verified'])->group(function () {
  
    Route::get('/messages/list', [MessageController::class, 'index'])->name('messages.index');

    Route::post('/messages/post', [MessageController::class, 'store'])->name('messages.store');;
});