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
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('reserved_event_id');

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('reserved_event_id')->references('reserved_event_id')->on('reserved_events')->onDelete('cascade');

            
            $table->decimal('amount_paid', 10, 2);
            $table->enum('status', ['pending','confirmed','cancelled'])->default('pending');
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
