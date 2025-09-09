<?php

// (opcional) database/migrations/xxxx_xx_xx_add_soft_deletes_to_registration_items.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('registration_items', function (Blueprint $table) {
      $table->softDeletes();
    });
  }
  public function down(): void {
    Schema::table('registration_items', function (Blueprint $table) {
      $table->dropSoftDeletes();
    });
  }
};

