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
        Schema::table('gym_passes', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('purchase_date'); // Lejárat ideje
            $table->softDeletes(); // Ez adja hozzá a deleted_at oszlopot
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gym_passes', function (Blueprint $table) {
            $table->dropColumn('expires_at');
            $table->dropSoftDeletes();
        });
    }
};
