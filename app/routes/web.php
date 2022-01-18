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

Route::group(['middleware' => ['auth'], 'as' => 'item.'], function() {
    Route::get('/items', ['App\Http\Controllers\ItemController', 'index'])->name('index');
    Route::get('/items/new', ['App\Http\Controllers\ItemController', 'new'])->name('new');
    Route::post('/items', ['App\Http\Controllers\ItemController', 'create'])->name('create');
    Route::get('/items/{id}', ['App\Http\Controllers\ItemController', 'show'])->name('show');
    Route::get('/items/{id}/edit', ['App\Http\Controllers\ItemController', 'edit'])->name('edit');
    Route::put('/items/{id}', ['App\Http\Controllers\ItemController', 'update'])->name('update');
    Route::delete('/items/{id}', ['App\Http\Controllers\ItemController', 'destroy'])->name('destroy');
});

Route::group(['middleware' => ['auth']], function() {
    Route::get('/users/{id}/rentals', ['App\Http\Controllers\RentalController', 'index'])->name('user.rentals');
    Route::post('/rentals', ['App\Http\Controllers\RentalController', 'create'])->name('rental.create');
    Route::delete('/rentals/{id}', ['App\Http\Controllers\RentalController', 'destroy'])->name('rental.destroy');
});

Route::group(['middleware' => ['auth']], function() {
    Route::get('/users/{id}/reservations', ['App\Http\Controllers\ReservationController', 'index'])->name('user.reservations');
    Route::get('/reservations/items/{id}', ['App\Http\Controllers\ReservationController', 'new'])->name('reservation.new');
    Route::post('/reservations/items', ['App\Http\Controllers\ReservationController', 'create'])->name('reservation.create');
    Route::delete('/reservations/{id}', ['App\Http\Controllers\ReservationController', 'destroy'])->name('reservation.destroy');
});
