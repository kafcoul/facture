<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'currency')) {
                $table->string('currency', 10)->default('XOF')->after('amount');
            }
            if (!Schema::hasColumn('payments', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('gateway');
            }
            if (!Schema::hasColumn('payments', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('metadata');
            }
            if (!Schema::hasColumn('payments', 'failed_at')) {
                $table->timestamp('failed_at')->nullable()->after('completed_at');
            }
            if (!Schema::hasColumn('payments', 'failure_reason')) {
                $table->text('failure_reason')->nullable()->after('failed_at');
            }
        });

        // Expand status enum to include 'completed' and 'refunded'
        // SQLite doesn't support ALTER COLUMN, so we handle it at model level
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['currency', 'payment_method', 'completed_at', 'failed_at', 'failure_reason']);
        });
    }
};
