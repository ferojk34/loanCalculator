<?php

namespace Modules\LoanCalculator\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Modules\Core\Http\Controllers\BaseController;
use Modules\LoanCalculator\Repositories\LoanCalculateRepository;
use Modules\LoanCalculator\Transformers\LoanCalculationResource;

class LoanCalculateController extends BaseController
{
    protected $repository;

    public function __construct(LoanCalculateRepository $repository)
    {
        $this->repository = $repository;
    }

    public function resource(array $calculatedResults): JsonResource
    {
        return new LoanCalculationResource((object)$calculatedResults);
    }

    public function calculateLoan(Request $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $calculatedResults = $this->repository->calculateLoan($request);
        } catch (Exception $exception) {
            DB::rollback();
            return $this->handleException($exception);
        }

        DB::commit();
        return $this->successResponse(
            payload: $this->resource($calculatedResults),
            message: "fetch-success",
        );
    }
}