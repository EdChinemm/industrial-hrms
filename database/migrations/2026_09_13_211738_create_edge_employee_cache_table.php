<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('edge')->create(
            'edge_employee_cache',
            function (Blueprint $table) {

                $table->id();

                $table->unsignedBigInteger(
                    'employee_id'
                )->unique();

                $table->string(
                    'employee_number'
                );

                $table->string(
                    'employee_name'
                );

                $table->string(
                    'barcode'
                )->unique();

                $table->boolean(
                    'is_active'
                )->default(true);

                $table->timestamp(
                    'cached_at'
                )->nullable();

                $table->timestamps();

                $table->index([
                    'employee_number',
                    'is_active',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::connection('edge')
            ->dropIfExists(
                'edge_employee_cache'
            );
    }
};