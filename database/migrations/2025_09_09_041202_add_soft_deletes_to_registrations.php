<?php

// database/migrations/xxxx_xx_xx_add_soft_deletes_to_registrations.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('registrations', function (Blueprint $table) {
      $table->softDeletes(); // adiciona deleted_at
    });
  }
  public function down(): void {
    Schema::table('registrations', function (Blueprint $table) {
      $table->dropSoftDeletes();
    });
  }
};

