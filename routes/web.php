<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PixAnalysisController;
use App\Http\Controllers\NumberPhoneController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\CnpjController;
use Illuminate\Http\Request;

Route::get('/upload', function () {
    return view('upload');
});


Route::get('/form', [PixAnalysisController::class, "showForm"]);
Route::post('/upload', [PixAnalysisController::class, 'store'])->name("store");

Route::get('/form-phone', [NumberPhoneController::class, 'formNumber'])->name('formNumber');
Route::post('/phone', [NumberPhoneController::class, 'getPhone'])->name('getPhone');


Route::get('/email-form', [EmailController::class, 'emailForm'])->name('emailForm');
Route::post('/email-send', [EmailController::class, 'getEmail'])->name('getEmail');


Route::get('/cnpj-form', [CnpjController::class, 'formCnpj'])->name('cnpjForm');
Route::post('/cnpj-send', [CnpjController::class, 'getCnpj'])->name('getCnpj');
