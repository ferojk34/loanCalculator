<?php

use Illuminate\Support\Facades\Route;
use Modules\LoanCalculator\Http\Controllers\LoanCalculateController;

Route::group(
    [
        "as" => "loan.",
        "prefix" => "public/loan",
        "middleware" => ["api"],
    ], function () {
    Route::post("calculate-loan", [LoanCalculateController::class, "calculateLoan"])->name('calculate');
});