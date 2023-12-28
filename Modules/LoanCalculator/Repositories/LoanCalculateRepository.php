<?php

namespace Modules\LoanCalculator\Repositories;

use Exception;
use Illuminate\Http\Request;
use Modules\Core\Repositories\BaseRepository;

class LoanCalculateRepository extends BaseRepository
{
    public function __construct()
    {
    }

    public function calculateLoan(Request $request)
    {
        try {

        } catch (Exception $exception) {
            throw $exception;
        }
    }
}