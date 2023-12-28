<?php

use Illuminate\Support\Facades\Route;
use Modules\LoanCalculator\Http\Controllers\LoanCalculateController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(
    [
        "as" => "loan.",
        "prefix" => "public/loan",
        "middleware" => ["api"],
    ], function () {
    Route::post("calculate-loan", [LoanCalculateController::class, "calculateLoan"])->name('calculate');
});