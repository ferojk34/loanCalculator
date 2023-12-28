<?php

namespace Modules\LoanCalculator\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoanAmortizationSchedule extends Model
{
    use HasFactory;

    protected $table = "loan_amortization_schedules";

    protected $fillable = [
        "month",
        "opening_balance",
        "monthly_payment",
        "principal_component",
        "interest_component",
        "closing_balance",
    ];
}
