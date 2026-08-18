<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Keep releases that still require payment out of the administrator review queue.
     */
    public function up(): void
    {
        DB::table('releases')
            ->where('billing_status', 'unpaid')
            ->where('distribution_status', 'pending')
            ->update(['distribution_status' => 'awaiting_payment']);
    }

    public function down(): void
    {
        DB::table('releases')
            ->where('distribution_status', 'awaiting_payment')
            ->update(['distribution_status' => 'pending']);
    }
};
