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
        Schema::create('releases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artist_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('type'); // single, album, ep
            $table->string('cover_image')->nullable();
            $table->string('genre');
            $table->string('language');
            $table->date('release_date')->nullable();
            $table->string('copyright_info')->nullable();
            $table->string('scheduling_type')->default('immediate'); // immediate, scheduled
            $table->string('distribution_status')->default('pending'); // pending, approved, distributed, rejected
            $table->text('rejection_reason')->nullable();
            $table->string('billing_status')->default('unpaid'); // unpaid, paid
            $table->decimal('price_paid', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('releases');
    }
};
