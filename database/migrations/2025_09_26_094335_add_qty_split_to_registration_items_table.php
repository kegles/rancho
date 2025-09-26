<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('registration_items', function (Blueprint $table) {
            // mantém qty antigo (se existir), adiciona qty_full/qty_half
            if (!Schema::hasColumn('registration_items', 'qty_full')) {
                $table->unsignedInteger('qty_full')->default(0)->after('product_id');
            }
            if (!Schema::hasColumn('registration_items', 'qty_half')) {
                $table->unsignedInteger('qty_half')->default(0)->after('qty_full');
            }
            // garante unit_price em centavos
            if (!Schema::hasColumn('registration_items', 'unit_price')) {
                $table->unsignedInteger('unit_price')->default(0)->after('qty_half');
            }
            // opcional: armazenar sku para rastreio (mesmo tendo product_id)
            if (!Schema::hasColumn('registration_items', 'sku')) {
                $table->string('sku', 50)->nullable()->after('product_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('registration_items', function (Blueprint $table) {
            if (Schema::hasColumn('registration_items', 'sku')) {
                $table->dropColumn('sku');
            }
            if (Schema::hasColumn('registration_items', 'unit_price')) {
                $table->dropColumn('unit_price');
            }
            if (Schema::hasColumn('registration_items', 'qty_half')) {
                $table->dropColumn('qty_half');
            }
            if (Schema::hasColumn('registration_items', 'qty_full')) {
                $table->dropColumn('qty_full');
            }
        });
    }
};
