<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('online_payment', function (Blueprint $table) {
            $table->id('online_payment_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('reserved_event_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_paid', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('online_payment');
    }
};
