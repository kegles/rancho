<?php

// database/migrations/2025_09_08_000000_create_core_tables.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('participants', function (Blueprint $t) {
      $t->id();
      $t->string('name');
      $t->string('callsign')->nullable();
      $t->string('city')->nullable();
      $t->string('email')->nullable();
      $t->string('phone')->nullable();
      $t->date('birthdate')->nullable();       // YYYY-MM-DD
      // R, C, A, CH(7�12), GUEST, ORG
      $t->string('category_code', 10);
      // null | AMADOR | REVENDEDOR
      $t->string('trade_role', 20)->nullable();
      // centavos para promessa de doa��o (revendedor)
      $t->unsignedInteger('trade_donation_pledge')->nullable();
      $t->timestamps();
    });

    Schema::create('registrations', function (Blueprint $t) {
      $t->id();
      $t->foreignId('participant_id')->constrained()->cascadeOnDelete();
      $t->enum('ticket_type', ['FULL','DAY']);  // FULL (2 dias) | DAY (1 dia)
      $t->text('days')->nullable();             // CSV das datas
      $t->boolean('is_exempt')->default(false); // isentos (GUEST/ORG)
      $t->unsignedInteger('base_price')->default(0);  // centavos
      $t->unsignedInteger('total_price')->default(0); // base + itens
      $t->boolean('eligible_draw')->default(false);   // concorre ao sorteio?
      $t->enum('status', ['PENDING','PAID','CANCELLED'])->default('PENDING');
      $t->string('reg_number')->unique();       // ex: 123
      $t->string('badge_letter', 2);            // R/C/A/K/G/O...
      $t->timestamps();
    });

    Schema::create('products', function (Blueprint $t) {
      $t->id();
      $t->string('sku')->unique();
      $t->string('name');
      $t->unsignedInteger('price')->default(0); // centavos
      $t->boolean('is_child_half')->default(false);   // meia para 7�12?
      $t->boolean('active')->default(true);
      $t->timestamps();
    });

    Schema::create('registration_items', function (Blueprint $t) {
      $t->id();
      $t->foreignId('registration_id')->constrained()->cascadeOnDelete();
      $t->foreignId('product_id')->constrained()->cascadeOnDelete();
      $t->unsignedInteger('qty')->default(1);
      $t->unsignedInteger('unit_price')->default(0); // j� com meia aplicada se for o caso
      $t->unsignedInteger('subtotal')->default(0);
      $t->timestamps();
    });

    Schema::create('draw_entries', function (Blueprint $t) {
      $t->id();
      $t->foreignId('registration_id')->constrained()->cascadeOnDelete();
      $t->string('draw_pool')->default('GERAL');
      $t->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('draw_entries');
    Schema::dropIfExists('registration_items');
    Schema::dropIfExists('products');
    Schema::dropIfExists('registrations');
    Schema::dropIfExists('participants');
  }
};
