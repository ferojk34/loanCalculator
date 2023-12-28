<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("loan_amortization_schedules", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("month");
            $table->decimal("opening_balance", 10, 2);
            $table->decimal("monthly_payment", 10, 2);
            $table->decimal("principal_component", 10, 2);
            $table->decimal("interest_component", 10, 2);
            $table->decimal("closing_balance", 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("loan_amortization_schedules");
    }
};
