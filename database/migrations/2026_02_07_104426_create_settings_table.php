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
        // 1. Tábla létrehozása
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // 2. Alapadatok beszúrása (sima insert-tel, ami nem ellenőriz előtte)
        DB::table('settings')->insert([
            [
                'key' => 'student_id_upload_required', 
                'value' => '0', 
                'created_at' => now(), 
                'updated_at' => now()
            ],
            [
                'key' => 'notify_purchase_success', 
                'value' => '1', 
                'created_at' => now(), 
                'updated_at' => now()
            ],
            [
                'key' => 'notify_reminder_email', 
                'value' => '1', 
                'created_at' => now(), 
                'updated_at' => now()
            ],
            [
                'key' => 'logging_enabled', 
                'value' => '1', 
                'created_at' => now(), 
                'updated_at' => now()
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
