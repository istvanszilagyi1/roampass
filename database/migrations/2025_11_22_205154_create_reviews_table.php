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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('gym_id')->constrained()->onDelete('cascade');
            $table->integer('rating'); // 1-től 5-ig
            $table->text('comment')->nullable();
            $table->timestamps();

            // Egy felhasználó egy termet csak egyszer értékelhessen
            $table->unique(['user_id', 'gym_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
