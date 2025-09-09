<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/xxxx_xx_xx_remove_birthdate_from_participants_table.php
return new class extends Migration {
    public function up(): void {
        Schema::table('participants', function (Blueprint $table) {
            $table->dropColumn('birthdate');
        });
    }
    public function down(): void {
        Schema::table('participants', function (Blueprint $table) {
            $table->date('birthdate')->nullable();
        });
    }
};
