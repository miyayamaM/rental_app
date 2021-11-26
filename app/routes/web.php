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

Route::get('/items', ['App\Http\Controllers\ItemController', 'index'])
                ->middleware('auth')
                ->name('item.index');

Route::get('/items/new', ['App\Http\Controllers\ItemController', 'new'])
                ->middleware('auth')
                ->name('item.new');

Route::post('/items', ['App\Http\Controllers\ItemController', 'create'])
                ->middleware('auth');
 
Route::get('/items/{id}', ['App\Http\Controllers\ItemController', 'show'])
                ->middleware('auth')
                ->name('item.show');
Route::get('/items/{id}/edit', ['App\Http\Controllers\ItemController', 'edit'])
                ->middleware('auth')
                ->name('item.edit');

Route::put('/items/{id}', ['App\Http\Controllers\ItemController', 'update'])
                ->middleware('auth')
                ->name('item.update');

Route::delete('/items/{id}', ['App\Http\Controllers\ItemController', 'destroy'])
                ->middleware('auth')
                ->name('item.destroy');

Route::get('/users/{id}/rentals', ['App\Http\Controllers\RentalController', 'index'])
                ->middleware('auth')
                ->name('user.rentals');
