<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_barcodes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->string('barcode', 100)->unique();

            $table->boolean('is_active')
                ->default(true);

            $table->dateTime('issued_at')
                ->nullable();

            $table->dateTime('revoked_at')
                ->nullable();

            $table->string('revoke_reason')
                ->nullable();

            $table->timestamps();

            $table->index([
                'employee_id',
                'is_active',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_barcodes');
    }
};
