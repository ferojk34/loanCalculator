<?php

namespace Modules\LoanCalculator\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExtraRepaymentSchedules extends Model
{
    use HasFactory;

    protected $table = "extra_repayment_schedules";

    protected $fillable = [
        "amortization_schedule_id",
        "monthly_payment",
        "closing_balance",
    ];
}
