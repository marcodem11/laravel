<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('reservations', function (Blueprint $table) {
            $table->index(['item_id', 'start_date']);
            $table->index(['item_id', 'end_date']);
        });
    }

    public function down(): void {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex(['item_id', 'start_date']);
            $table->dropIndex(['item_id', 'end_date']);
        });
    }
};