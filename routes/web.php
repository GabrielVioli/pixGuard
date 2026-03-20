<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PixAnalysisController;
use App\Http\Controllers\NumberPhoneController;
use Illuminate\Http\Request;

Route::get('/upload', function () {
    return view('upload');
});


Route::get('/form', [PixAnalysisController::class, "showForm"]);
Route::post('/upload', [PixAnalysisController::class, 'store'])->name("store");

Route::get('/form-phone', [NumberPhoneController::class, 'formNumber'])->name('formNumber');
Route::post('/phone', [NumberPhoneController::class, 'getPhone'])->name('getPhone');
