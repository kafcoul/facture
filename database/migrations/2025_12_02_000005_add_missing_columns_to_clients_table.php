<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'city')) {
                $table->string('city')->nullable()->after('address');
            }
            if (!Schema::hasColumn('clients', 'country')) {
                $table->string('country')->nullable()->after('city');
            }
            if (!Schema::hasColumn('clients', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('country');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['city', 'country', 'is_active']);
        });
    }
};
