<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('gyms', function (Blueprint $table) {
            // 10 számjegy összesen, 2 tizedesjegy (pénzösszegeknél ez a szabvány)
            $table->decimal('payout_per_scan', 10, 2)->default(1000.00)->after('tax_number');
        });
    }

    public function down()
    {
        Schema::table('gyms', function (Blueprint $table) {
            $table->dropColumn('payout_per_scan');
        });
    }
};
