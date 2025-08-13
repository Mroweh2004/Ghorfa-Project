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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('property_type');
            $table->string('listing_type');
            $table->string('country');
            $table->string('city');
            $table->string('address');
            $table->double('price');
            $table->double('area_m3')->nullable(); 
            $table->integer('room_nb')->nullable();
            $table->integer('bathroom_nb')->nullable(); 
            $table->integer('bedroom_nb')->nullable(); 
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
