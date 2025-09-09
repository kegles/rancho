<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/xxxx_xx_xx_change_trade_donation_to_decimal.php
return new class extends Migration {
    public function up(): void {
        Schema::table('participants', function (Blueprint $table) {
            $table->decimal('trade_donation_pledge', 8, 2)->nullable()->change();
        });
    }
    public function down(): void {
        Schema::table('participants', function (Blueprint $table) {
            $table->unsignedInteger('trade_donation_pledge')->nullable()->change();
        });
    }
};
