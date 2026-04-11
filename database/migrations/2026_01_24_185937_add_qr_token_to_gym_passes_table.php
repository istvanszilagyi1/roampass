<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 0. Lépés: HIBAJAVÍTÁS - Ha maradt volna 'qr_token' az előző hibás futásból, töröljük
        if (Schema::hasColumn('gym_passes', 'qr_token')) {
            Schema::table('gym_passes', function (Blueprint $table) {
                $table->dropColumn('qr_token');
            });
        }

        // 1. Lépés: Hozzáadjuk az oszlopot (NULLABLE)
        Schema::table('gym_passes', function (Blueprint $table) {
            $table->string('qr_token', 64)->nullable()->after('id');
        });

        // 2. Lépés: Feltöltjük a meglévő sorokat egyedi tokenekkel
        $passes = DB::table('gym_passes')->get();

        foreach ($passes as $pass) {
            DB::table('gym_passes')
                ->where('id', $pass->id)
                ->update(['qr_token' => Str::random(32)]);
        }

        // 3. Lépés: Most, hogy van adat, rátesszük a UNIQUE és NOT NULL szabályt
        Schema::table('gym_passes', function (Blueprint $table) {
            $table->string('qr_token', 64)->nullable(false)->change();
            $table->unique('qr_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('gym_passes', 'qr_token')) {
            Schema::table('gym_passes', function (Blueprint $table) {
                // Először el kell dobni az indexet, ha létezik
                $table->dropUnique(['qr_token']); 
                $table->dropColumn('qr_token');
            });
        }
    }
};