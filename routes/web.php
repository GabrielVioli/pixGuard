<?php

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\Sandbox\CnpjController;
use App\Http\Controllers\Sandbox\CpfController;
use App\Http\Controllers\Sandbox\EmailController;
use App\Http\Controllers\Sandbox\PhoneNumberController;
use App\Http\Controllers\Sandbox\PixAnalysisController;
use Illuminate\Support\Facades\Route;

Route::get('GeralForm', [AnalysisController::class, "geralForm"]);
Route::post("verify", [AnalysisController::class, "verify"])->name("pix.verify");


Route::prefix('sandbox')->group(function () {
    Route::get('/upload', [AnalysisController::class, 'uploadForm']);
    Route::get('/form', [AnalysisController::class, 'uploadForm']);
    Route::post('/upload', [PixAnalysisController::class, 'store'])->name('store');

    Route::get('/form-phone', [AnalysisController::class, 'phoneForm'])->name('formNumber');
    Route::post('/phone', [PhoneNumberController::class, 'getPhone'])->name('getPhone');

    Route::get('/email-form', [AnalysisController::class, 'emailForm'])->name('emailForm');
    Route::post('/email-send', [EmailController::class, 'getEmail'])->name('getEmail');

    Route::get('/cnpj-form', [AnalysisController::class, 'cnpjForm'])->name('cnpjForm');
    Route::post('/cnpj-send', [CnpjController::class, 'getCnpj'])->name('getCnpj');

    Route::get('/cpf-form', [AnalysisController::class, 'cpfForm'])->name('cpfForm');
    Route::post('/cpf-send', [CpfController::class, 'getCpf'])->name('getCpf');

});
