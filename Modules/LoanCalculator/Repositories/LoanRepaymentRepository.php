<?php

namespace Modules\LoanCalculator\Repositories;

use Modules\Core\Repositories\BaseRepository;
use Modules\LoanCalculator\Entities\ExtraRepaymentSchedule;

class LoanRepaymentRepository extends BaseRepository
{
    public function __construct(ExtraRepaymentSchedule $extraRepaymentSchedule)
    {
        $this->model = $extraRepaymentSchedule;
    }
}