<?php

namespace Modules\LoanCalculator\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class LoanCalculationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            "schedules" => $this->resource->schedule,
            "effective_interest_rate" => $this->resource->effective_interest_rate ?? "",
        ];
    }
}
