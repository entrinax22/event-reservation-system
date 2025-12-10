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
        Schema::create('reserved_materials', function (Blueprint $table) {
            $table->id('reserved_material_id');
            $table->unsignedBigInteger('reserved_event_id');
            $table->unsignedBigInteger('material_id');

            $table->foreign('reserved_event_id')->references('reserved_event_id')->on('reserved_events')->onDelete('cascade');
            $table->foreign('material_id')->references('material_id')->on('materials')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserved_materials');
    }
};
