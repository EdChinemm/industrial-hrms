<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('edge')->create(
            'edge_device_config',
            function (Blueprint $table) {

                $table->id();

                $table->string(
                    'device_id'
                )->unique();

                $table->string(
                    'device_name'
                );

                $table->string(
                    'location'
                )->nullable();

                $table->boolean(
                    'is_active'
                )->default(true);

                $table->timestamp(
                    'cached_at'
                )->nullable();

                $table->timestamps();
            }
        );
    }

    public function down(): void
    {
        Schema::connection('edge')
            ->dropIfExists(
                'edge_device_config'
            );
    }
};