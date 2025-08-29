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
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('client_name');
            $table->string('client_contact_information');
            $table->dateTime('event_date');
            $table->dateTime('event_end_date')->nullable();
            $table->decimal('total_cost', 10, 2)->default(0);
            $table->string('event_notes')->nullable();
            $table->enum('status', ['pending','accepted','paid_downpayment', 'completed', 'cancelled'])->default('pending');
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
