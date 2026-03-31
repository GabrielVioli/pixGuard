<?php

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\PixAnalysisController as PublicPixAnalysisController;
use Illuminate\Support\Facades\Route;

Route::get('GeralForm', [AnalysisController::class, "geralForm"]);
Route::post("verify", [AnalysisController::class, "verify"])->name("pix.verify");

Route::get('/form', [PublicPixAnalysisController::class, 'showForm']);
Route::post('/upload', [PublicPixAnalysisController::class, 'store'])->name('upload.store');

require __DIR__.'/sandbox.php';

