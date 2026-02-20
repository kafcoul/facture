<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('products', 'unit_price')) {
            Schema::table('products', function (Blueprint $table) {
                $table->decimal('unit_price', 12, 2)->default(0)->after('price');
            });
        }

        // Sync existing price data into unit_price
        DB::statement('UPDATE products SET unit_price = price WHERE unit_price = 0 AND price IS NOT NULL');
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'unit_price')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('unit_price');
            });
        }
    }
};
