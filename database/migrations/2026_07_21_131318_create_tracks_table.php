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
        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('release_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('artist_name');
            $table->string('composer')->nullable();
            $table->string('songwriter')->nullable();
            $table->string('isrc')->nullable();
            $table->string('audio_file');
            $table->integer('duration')->default(0); // duration in seconds
            $table->integer('streams_count')->default(0);
            $table->integer('downloads_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracks');
    }
};
