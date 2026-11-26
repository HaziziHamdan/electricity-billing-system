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
        Schema::create('electricity_usages', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name', 150);
            $table->foreignId('building_type_id')
                ->constrained()
                ->restrictOnDelete();
            $table->unsignedTinyInteger('month');
            $table->unsignedInteger('usage_kwh');
            $table->foreignId('state_id')
                ->constrained()
                ->restrictOnDelete();
            $table->decimal('bill_amount', 12, 2);          // frozen at time of calculation
            $table->jsonb('tariff_snapshot');
            $table->timestamps();
            $table->softDeletes();
            $table->index('building_type_id');
            $table->index('state_id');
            $table->index('usage_kwh');
            $table->index(['month', 'building_type_id']);
        });

        DB::statement('ALTER TABLE electricity_usages ADD CONSTRAINT electricity_usages_month_range CHECK (month BETWEEN 1 AND 12)');
        DB::statement('ALTER TABLE electricity_usages ADD CONSTRAINT electricity_usages_usage_non_negative CHECK (usage_kwh >= 0)');
        DB::statement('ALTER TABLE electricity_usages ADD CONSTRAINT electricity_usages_bill_non_negative CHECK (bill_amount >= 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('electricity_usages');
    }
};
