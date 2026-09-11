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
        Schema::table('positions', function (Blueprint $table) {

            $table->foreignId('department_id')
                ->after('id')
                ->constrained('departments')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('title', 100)
                ->after('department_id');

            $table->string('code', 30)
                ->unique()
                ->after('title');

            $table->text('description')
                ->nullable()
                ->after('code');

            $table->boolean('is_active')
                ->default(true)
                ->after('description');

            $table->index([
                'department_id',
                'is_active',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {

            $table->dropIndex([
                'department_id',
                'is_active',
            ]);

            $table->dropUnique([
                'code',
            ]);

            $table->dropForeign([
                'department_id',
            ]);

            $table->dropColumn([
                'department_id',
                'title',
                'code',
                'description',
                'is_active',
            ]);
        });
    }
};
