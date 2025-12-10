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
        Schema::create('reserved_events', function (Blueprint $table) {
            $table->id('reserved_event_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('event_id');
            
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('event_id')->references('event_id')->on('events')->onDelete('cascade');

            $table->dateTime('event_date');
            $table->dateTime('event_end_date')->nullable();
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->string('event_notes')->nullable();
            $table->decimal('downpayment_amount', 10, 2)->default(0);
            $table->enum('status', ['pending','accepted', 'downpayment_update','completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserved_events');
    }
};
