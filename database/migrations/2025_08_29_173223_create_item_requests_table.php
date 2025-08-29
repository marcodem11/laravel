<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('item_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['inventory', 'to-buy']);
            $table->foreignId('item_id')->nullable()->constrained()->onDelete('cascade'); 
            $table->integer('quantity')->default(1);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('note')->nullable(); // utile per "to-buy"
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('item_requests');
    }
};