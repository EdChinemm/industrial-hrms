<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_sync_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('attendance_device_id')
                ->constrained('attendance_devices')
                ->cascadeOnDelete();

            $table->string('sync_reference')->unique();

            $table->dateTime('sync_started_at');
            $table->dateTime('sync_completed_at')->nullable();

            $table->unsignedInteger('records_processed')->default(0);
            $table->unsignedInteger('records_synced')->default(0);
            $table->unsignedInteger('records_failed')->default(0);

            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'partial',
                'failed'
            ])->default('pending');

            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('sync_started_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_sync_logs');
    }
};
