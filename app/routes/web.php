<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
Route::get('/items', ['App\Http\Controllers\ItemController', 'index']);
Route::get('/items/new', ['App\Http\Controllers\ItemController', 'new']);
Route::post('/items', ['App\Http\Controllers\ItemController', 'create']);
Route::get('/items/{id}', ['App\Http\Controllers\ItemController', 'show'])->name('item.show');
Route::get('/items/{id}/edit', ['App\Http\Controllers\ItemController', 'edit']);
Route::post('/items/{id}', ['App\Http\Controllers\ItemController', 'update']);
Route::delete('/items/{id}', ['App\Http\Controllers\ItemController', 'destroy'])->name('item.destroy');
