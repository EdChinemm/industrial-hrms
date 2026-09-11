<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->date('attendance_date');

            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();

            $table->enum('status', [
                'present',
                'absent',
                'late',
                'half_day',
                'on_leave',
                'holiday'
            ])->default('present');

            $table->string('attendance_source')
                ->default('edge_device');

            $table->string('device_id')->nullable();

            $table->string('sync_status')
                ->default('pending');

            $table->dateTime('synced_at')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->unique([
                'employee_id',
                'attendance_date'
            ]);

            $table->index('attendance_date');
            $table->index('status');
            $table->index('sync_status');
            $table->index('device_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
