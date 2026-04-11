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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('wants_newsletter')->default(false);
            $table->timestamp('privacy_policy_accepted_at')->nullable();
        });

        // 💡 EXTRA LÉPÉS: A régi felhasználók adatainak frissítése
        // Minden meglévő felhasználónál beállítjuk a mostani időpontot elfogadásnak.
        \App\Models\User::whereNull('privacy_policy_accepted_at')
            ->update(['privacy_policy_accepted_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['wants_newsletter', 'privacy_policy_accepted_at']);
        });
    }
};
