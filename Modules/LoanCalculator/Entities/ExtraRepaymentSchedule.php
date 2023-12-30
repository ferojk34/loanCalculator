<?php

namespace Modules\LoanCalculator\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExtraRepaymentSchedule extends Model
{
    use HasFactory;

    protected $table = "extra_repayment_schedules";

    protected $fillable = [
        "month",
        "opening_balance",
        "monthly_payment",
        "principal_component",
        "interest_component",
        "closing_balance",
        "extra_payment",
        "remaining_loan_term",
    ];
}
