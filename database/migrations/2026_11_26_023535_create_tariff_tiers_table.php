<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tariff_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_type_id')
                ->constrained()
                ->restrictOnDelete();
            $table->unsignedInteger('up_to_kwh')->nullable();
            $table->decimal('rate', 8, 4);          // RM per kWh
            $table->timestamps();
            $table->unique(['building_type_id', 'up_to_kwh']);
        });

        DB::statement(
            'CREATE UNIQUE INDEX tariff_tiers_one_open_tier_per_type
             ON tariff_tiers (building_type_id)
             WHERE up_to_kwh IS NULL'
        );

        DB::statement('ALTER TABLE tariff_tiers ADD CONSTRAINT tariff_tiers_rate_positive CHECK (rate > 0)');
        DB::statement('ALTER TABLE tariff_tiers ADD CONSTRAINT tariff_tiers_up_to_kwh_positive CHECK (up_to_kwh IS NULL OR up_to_kwh > 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tariff_tiers');
    }
};
