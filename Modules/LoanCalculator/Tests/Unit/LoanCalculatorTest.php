<?php

namespace Modules\LoanCalculator\Tests\Unit;

use Illuminate\Http\Response;
use Modules\Core\Tests\StoreFrontBaseTestCase;
use Modules\LoanCalculator\Entities\ExtraRepaymentSchedule;
use Modules\LoanCalculator\Entities\LoanAmortizationSchedule;

class LoanCalculatorTest extends StoreFrontBaseTestCase
{
    public array $headers;
    protected  $extra_payment_schedule;

    public function setUp(): void
    {
        $this->model = LoanAmortizationSchedule::class;
        $this->extra_payment_schedule = ExtraRepaymentSchedule::class;

        parent::setUp();
        $this->createHeader();
        $this->route_prefix = "loan.";
    }

    public function testValidateLoanCalculatorInputs(): void
    {
        $postData = [
            'loanAmount' => -1000,
            'annualInterestRate' => 5,
            'loanTermInYears' => 30,
            'additionalPayment' => 100,
        ];

        $response = $this->withHeaders($this->headers)->post($this->getRoute("calculate"), $postData);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testStoreAmortizationScheduleWithoutRepayment(): void
    {
        $postData = [
            'loanAmount' => 1000,
            'annualInterestRate' => 5,
            'loanTermInYears' => 3,
        ];
        $response = $this->withHeaders($this->headers)->json("POST", $this->getRoute("calculate"), $postData);
        $response->assertOk();
    }

    public function testStoreAmortizationScheduleWithRepayment(): void
    {
        $postData = [
            'loanAmount' => 1000,
            'annualInterestRate' => 5,
            'loanTermInYears' => 3,
            'additionalPayment' => 100,
        ];
        $response = $this->withHeaders($this->headers)->json("POST", $this->getRoute("calculate"), $postData);
        $response->assertOk();
    }
}
