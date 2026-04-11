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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            // Összekötjük az edzőteremmel:
            $table->foreignId('gym_id')->constrained()->onDelete('cascade');
            
            $table->string('invoice_number'); // Számlaszám (pl. SZ-2026-001)
            $table->decimal('amount', 10, 2); // Összeg (Ft)
            $table->string('pdf_url');        // A link, ahol a partner le tudja tölteni a számlát
            $table->date('issue_date');       // Mikor jött létre a számla?
            $table->string('status')->default('unpaid'); // Fizetve, Nincs fizetve, stb.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
