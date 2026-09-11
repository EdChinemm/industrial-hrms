<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            /*
             * Organizational assignment
             */
            $table->foreignId('department_id')
                ->constrained('departments')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('position_id')
                ->constrained('positions')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            /*
             * Employee identification
             */
            $table->string('employee_number', 30)->unique();

            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);

            /*
             * Personal information
             */
            $table->string('gender', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('national_id', 50)->nullable()->unique();

            /*
             * Contact information
             */
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable()->unique();
            $table->text('address')->nullable();

            /*
             * Employment information
             */
            $table->date('employment_date')->nullable();
            $table->date('termination_date')->nullable();

            $table->enum('employment_status', [
                'active',
                'inactive',
                'suspended',
                'terminated',
            ])->default('active');

            /*
             * Record management
             */
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            /*
             * Frequently used indexes
             */
            $table->index(['department_id', 'employment_status']);
            $table->index(['position_id', 'employment_status']);
            $table->index('last_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
