<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('registration_attendees', function (Blueprint $table) {
            if (!Schema::hasColumn('registration_attendees','callsign')) {
                $table->string('callsign', 20)->nullable()->after('name');
            }
        });
    }
    public function down(): void
    {
        Schema::table('registration_attendees', function (Blueprint $table) {
            if (Schema::hasColumn('registration_attendees','callsign')) {
                $table->dropColumn('callsign');
            }
        });
    }
};
