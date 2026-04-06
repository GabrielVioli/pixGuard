<?php

use App\Http\Controllers\AnalysisController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AnalysisController::class, "geralForm"]);
Route::post("verify", [AnalysisController::class, "verify"])->name("pix.verify");
