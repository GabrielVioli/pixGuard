<?php

use App\Http\Controllers\Sandbox\CnpjController;
use App\Http\Controllers\Sandbox\CpfController;
use App\Http\Controllers\Sandbox\EmailController;
use App\Http\Controllers\Sandbox\PhoneNumberController;
use App\Http\Controllers\Sandbox\PixAnalysisController;
use Illuminate\Support\Facades\Route;

Route::get('/email-form', [EmailController::class, 'emailForm'])
    ->name('sandbox.email.form');
Route::post('/email-send', [EmailController::class, 'getEmail'])
    ->name('sandbox.email.send');

Route::get('/cnpj-form', [CnpjController::class, 'formCnpj'])
    ->name('sandbox.cnpj.form');
Route::post('/cnpj-send', [CnpjController::class, 'getCnpj'])
    ->name('sandbox.cnpj.send');

Route::get('/cpf-form', [CpfController::class, 'formCpf'])
    ->name('sandbox.cpf.form');
Route::post('/cpf-send', [CpfController::class, 'getCpf'])
    ->name('sandbox.cpf.send');

Route::get('/form-phone', [PhoneNumberController::class, 'formPhone'])
    ->name('sandbox.phone.form');
Route::post('/phone', [PhoneNumberController::class, 'getPhone'])
    ->name('sandbox.phone.send');

Route::get('/form', [PixAnalysisController::class, 'showForm'])
    ->name('sandbox.pix.form');
Route::post('/upload', [PixAnalysisController::class, 'store'])
    ->name('store');
