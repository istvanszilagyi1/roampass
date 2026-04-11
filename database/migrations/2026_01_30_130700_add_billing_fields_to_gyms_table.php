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
        Schema::table('gyms', function (Blueprint $table) {
            $table->string('billing_name')->nullable();    // Hivatalos cégnév (pl. Kovács Fitness Kft.)
            $table->string('billing_address')->nullable(); // Székhely címe
            $table->string('tax_number')->nullable();      // Adószám (fontos a számlázáshoz!)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gyms', function (Blueprint $table) {
            //
        });
    }
};
