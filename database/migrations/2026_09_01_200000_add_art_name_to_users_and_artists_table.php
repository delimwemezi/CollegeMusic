<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds art_name (stage name / label name) to users and artists tables.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('art_name')->nullable()->after('name');
        });

        Schema::table('artists', function (Blueprint $table) {
            $table->string('art_name')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('art_name');
        });

        Schema::table('artists', function (Blueprint $table) {
            $table->dropColumn('art_name');
        });
    }
};
