<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;

Route::get('/', function () {
    return view('welcome');
});

// Public file streaming for books (PDF inline)
Route::get('/books/{id}/file', [BookController::class, 'file'])->whereNumber('id');

// SPA fallback to support Vue Router routes on refresh
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
