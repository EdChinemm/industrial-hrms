<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('edge')
            ->create('edge_attendance_queue', function (Blueprint $table) {

                $table->id();

                $table->string('local_reference', 100)
                    ->unique();

                $table->unsignedBigInteger('employee_id');

                $table->string('employee_number', 30);

                $table->string('employee_name');

                $table->string('barcode', 100);

                $table->string('device_id', 100);

                $table->date('attendance_date');

                $table->dateTime('check_in')
                    ->nullable();

                $table->dateTime('check_out')
                    ->nullable();

                $table->string('status')
                    ->default('present');

                $table->string('sync_status')
                    ->default('pending');

                $table->dateTime('synced_at')
                    ->nullable();

                $table->text('sync_error')
                    ->nullable();

                $table->timestamps();

                $table->index([
                    'device_id',
                    'sync_status',
                ]);

                $table->index([
                    'employee_id',
                    'attendance_date',
                ]);
            });
    }

    public function down(): void
    {
        Schema::connection('edge')
            ->dropIfExists('edge_attendance_queue');
    }
};
