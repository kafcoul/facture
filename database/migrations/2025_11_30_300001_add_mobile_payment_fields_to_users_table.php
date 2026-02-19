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
        Schema::table('users', function (Blueprint $table) {
            // Informations entreprise supplémentaires
            if (!Schema::hasColumn('users', 'company_name')) {
                $table->string('company_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'tax_id')) {
                $table->string('tax_id')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            
            // Numéros de paiement mobile
            if (!Schema::hasColumn('users', 'wave_number')) {
                $table->string('wave_number')->nullable();
            }
            if (!Schema::hasColumn('users', 'orange_money_number')) {
                $table->string('orange_money_number')->nullable();
            }
            if (!Schema::hasColumn('users', 'momo_number')) {
                $table->string('momo_number')->nullable();
            }
            if (!Schema::hasColumn('users', 'moov_money_number')) {
                $table->string('moov_money_number')->nullable();
            }
            
            // Template de facture sélectionné
            if (!Schema::hasColumn('users', 'invoice_template')) {
                $table->string('invoice_template')->default('starter');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'wave_number',
                'orange_money_number', 
                'momo_number',
                'moov_money_number',
                'company_name',
                'address',
                'tax_id',
                'phone',
                'invoice_template'
            ]);
        });
    }
};
