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
            // Hozzáadjuk a számot. Nullable-re hagyjuk, mert regisztrációkor még nem kötelező.
            $table->string('student_id_number')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
        // Csak akkor törölje, ha létezik
        if (Schema::hasColumn('users', 'student_id_number')) {
            $table->dropColumn('student_id_number');
        }
    });
    }
};
