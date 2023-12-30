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

    public function resource(object $calculatedResults): JsonResource
    {
        return new LoanCalculationResource($calculatedResults);
    }

    public function calculateLoan(Request $request): JsonResponse
    {
        try {
            $calculatedResults = $this->repository->calculateLoan($request);
        } catch (Exception $exception) {
            return $this->handleException($exception);
        }

        return $this->successResponse(
            payload: $this->resource((object)$calculatedResults),
            message: "fetch-success",
        );
    }
}