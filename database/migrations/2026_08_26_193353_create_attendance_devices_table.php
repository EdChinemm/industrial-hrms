<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_devices', function (Blueprint $table) {
            $table->id();

            $table->string('device_id')->unique();
            $table->string('device_name');

            $table->string('location')->nullable();
            $table->string('device_type')->default('edge_terminal');

            $table->string('ip_address')->nullable();
            $table->string('mac_address')->nullable();

            $table->boolean('is_active')->default(true);

            $table->dateTime('last_sync_at')->nullable();

            $table->text('description')->nullable();

            $table->timestamps();

            $table->index('is_active');
            $table->index('location');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_devices');
    }
};
