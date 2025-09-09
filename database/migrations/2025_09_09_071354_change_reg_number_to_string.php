<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/xxxx_xx_xx_change_reg_number_to_string.php
return new class extends Migration {
  public function up(): void {
    Schema::table('registrations', function (Blueprint $t) {
      $t->string('reg_number', 16)->change();
    });
  }
  public function down(): void {
    Schema::table('registrations', function (Blueprint $t) {
      $t->unsignedInteger('reg_number')->change(); // só se antes fosse int
    });
  }
};
