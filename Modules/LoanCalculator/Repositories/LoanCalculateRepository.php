<?php

namespace Modules\LoanCalculator\Repositories;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Core\Repositories\BaseRepository;
use Modules\LoanCalculator\Entities\LoanAmortizationSchedule;

class LoanCalculateRepository extends BaseRepository
{
    protected object $loanRepaymentRepository;

    public function __construct(LoanAmortizationSchedule $loanSchedule, LoanRepaymentRepository $loanRepaymentRepository)
    {
        $this->model = $loanSchedule;
        $this->loanRepaymentRepository = $loanRepaymentRepository;
    }

    public function calculateLoan(Request $request): array
    {
        DB::beginTransaction();

        try {
            $this->rules = [
                "loanAmount" => "required|numeric|min:1|max:1000000|regex:/^\d+$/",
                "annualInterestRate" => "required|numeric|min:1|max:100|regex:/^\d+$/",
                "loanTermInYears" => "required|numeric|min:1|max:15|regex:/^\d+$/",
                "additionalPayment" => "sometimes|nullable|numeric|min:1|max:100000|regex:/^\d+$/",
            ];
            $this->validateData($request);

            $amortizationSchedule = $this->generateAmortizationSchedule(
                loanAmount: $request->loanAmount,
                annualInterestRate: $request->annualInterestRate,
                loanTermInYears: $request->loanTermInYears,
                additionalPayment: $request->additionalPayment
            );

            $tableName = $request->additionalPayment
                ? "extra_repayment_schedules"
                : "loan_amortization_schedules";
            DB::table($tableName)->insert($amortizationSchedule["schedule"]);

        } catch (Exception $exception) {
            DB::rollback();
            throw $exception;
        }

        DB::commit();
        return $amortizationSchedule;
    }

    private function generateAmortizationSchedule(
        float $loanAmount,
        int $annualInterestRate,
        int $loanTermInYears,
        ?float $additionalPayment = 0
    ): array {
        try {
            $monthlyInterestRate = ($annualInterestRate / 12) / 100;
            $numberOfMonths = $loanTermInYears * 12;
            $userExtraPayment = $additionalPayment;
            $schedule = [];
            $remainingBalance = $loanAmount;
            $totalInterestPaid = 0;
            for ($i = 1; $i <= $numberOfMonths; $i++) {
                $openingBalance = $remainingBalance;
                $totalPrincipal = 0;
                $monthlyPayment = ($loanAmount * $monthlyInterestRate) / (1 - pow(1 + $monthlyInterestRate, -$numberOfMonths));
                $interestComponent = $remainingBalance * $monthlyInterestRate;
                $principalComponent = $monthlyPayment - $interestComponent;
               if ($principalComponent > $remainingBalance) {
                   $principalComponent  = $remainingBalance;
                   $additionalPayment = 0;
                   $totalPrincipal = $principalComponent;
                   $monthlyPayment = $interestComponent + $remainingBalance;
               } elseif (($principalComponent + $additionalPayment) > $remainingBalance) {
                    $additionalPayment = $remainingBalance - $principalComponent;
                    $totalPrincipal = $principalComponent + $additionalPayment;
               } else {
                    $totalPrincipal = $principalComponent + $additionalPayment;
               }

                $remainingBalance -= $totalPrincipal;
                $totalInterestPaid += $interestComponent;

                $monthlyLoanSchedule = [
                    "month" => $i,
                    "opening_balance" => $openingBalance,
                    "monthly_payment" => $monthlyPayment,
                    "interest_component" => $interestComponent,
                    "principal_component" => $principalComponent,
                    "closing_balance" => $remainingBalance,
                ];

                if ($userExtraPayment) {
                    $monthlyLoanSchedule["extra_payment"] = $additionalPayment;
                    $monthlyLoanSchedule["remaining_loan_term"] = $numberOfMonths - $i;
                }
                $schedule[] = $monthlyLoanSchedule;

                 // if the remaining balance becomes zero
                if ($remainingBalance <= 0) {
                    break;
                }
            }

            $data["schedule"] = $schedule;
            if ($userExtraPayment) {
                $data["effective_interest_rate"] = ($totalInterestPaid / $loanAmount) * 100;
            }

        } catch (Exception $exception) {
            throw $exception;
        }

        return $data;
    }
}