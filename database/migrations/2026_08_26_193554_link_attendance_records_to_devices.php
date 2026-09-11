<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropIndex([
                'device_id'
            ]);

            $table->foreign('device_id')
                ->references('device_id')
                ->on('attendance_devices')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropForeign([
                'device_id'
            ]);

            $table->index('device_id');
        });
    }
};
