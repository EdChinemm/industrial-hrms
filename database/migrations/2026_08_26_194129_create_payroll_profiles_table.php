<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->unique()
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->decimal('basic_salary', 12, 2)->default(0);

            $table->enum('pay_frequency', [
                'monthly',
                'weekly',
                'daily',
                'hourly'
            ])->default('monthly');

            $table->decimal('hourly_rate', 12, 2)->nullable();

            $table->decimal('standard_hours_per_day', 5, 2)
                ->default(8);

            $table->unsignedInteger('standard_working_days')
                ->default(22);

            $table->boolean('overtime_enabled')->default(false);

            $table->decimal('overtime_rate_multiplier', 5, 2)
                ->default(1.50);

            $table->boolean('is_active')->default(true);

            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();

            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_profiles');
    }
};
