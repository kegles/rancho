<?php

// database/migrations/xxxx_xx_xx_create_registration_attendees_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('registration_attendees', function (Blueprint $t) {
      $t->id();
      $t->foreignId('registration_id')->constrained()->cascadeOnDelete();
      $t->enum('role', ['PRIMARY','SPOUSE','ACCOMP','CHILD']); // PRIMARY é opcional (podemos não salvar o titular)
      $t->string('name'); // nome do cônjuge/acomp/criança
      $t->timestamps();
    });
    // Opcional: se quiser limpar legado de categorias antigas nos participantes
    Schema::table('participants', function (Blueprint $t) {
      $t->string('category_code', 2)->default('V')->change(); // 'V','R','E'
    });
  }
  public function down(): void {
    Schema::dropIfExists('registration_attendees');
  }
};
