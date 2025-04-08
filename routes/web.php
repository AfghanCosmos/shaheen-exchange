<?php

use App\Http\Controllers\HawlaPrintController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/hawla/{hawla}/print', [HawlaPrintController::class, 'print'])
    ->middleware(['auth']) // or 'web'
    ->name('hawla.print');
