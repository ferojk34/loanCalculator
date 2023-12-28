<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("extra_repayment_schedules", function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("amortization_schedule_id");
            $table->foreign("amortization_schedule_id")
                ->references("id")
                ->on("loan_amortization_schedules")
                ->onDelete("cascade");

            $table->decimal("monthly_payment", 10, 2);
            $table->decimal("closing_balance", 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("extra_repayment_schedules");
    }
};
