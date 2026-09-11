<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_calculations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payroll_period_id')
                ->constrained('payroll_periods')
                ->cascadeOnDelete();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->decimal('basic_salary', 12, 2)->default(0);

            $table->unsignedInteger('expected_working_days')
                ->default(0);

            $table->unsignedInteger('days_present')
                ->default(0);

            $table->unsignedInteger('days_absent')
                ->default(0);

            $table->decimal('hours_worked', 10, 2)
                ->default(0);

            $table->decimal('overtime_hours', 10, 2)
                ->default(0);

            $table->decimal('estimated_basic_earnings', 12, 2)
                ->default(0);

            $table->decimal('estimated_overtime_earnings', 12, 2)
                ->default(0);

            $table->decimal('estimated_total_earnings', 12, 2)
                ->default(0);

            $table->enum('calculation_status', [
                'draft',
                'calculated',
                'reviewed'
            ])->default('draft');

            $table->dateTime('calculated_at')->nullable();

            $table->text('calculation_notes')->nullable();

            $table->timestamps();

            $table->unique([
                'payroll_period_id',
                'employee_id'
            ]);

            $table->index('calculation_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_calculations');
    }
};
