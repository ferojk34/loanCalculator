<?php

namespace Modules\LoanCalculator\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\Request;
use Modules\Core\Http\Controllers\BaseController;
use Modules\LoanCalculator\Repositories\LoanCalculateRepository;

class LoanCalculateController extends BaseController
{
    protected $repository;

    public function __construct(LoanCalculateRepository $repository)
    {
        $this->repository = $repository;
    }

    public function calculateLoan(Request $request)
    {
        try {

        } catch (Exception $exception) {
            //
        }
    }
}