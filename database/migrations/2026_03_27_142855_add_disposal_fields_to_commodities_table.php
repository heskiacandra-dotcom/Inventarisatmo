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
        Schema::table('commodities', function (Blueprint $table) {
            $table->tinyInteger('aktif')->default(1)->after('note')->comment('1 = aktif, 0 = dilepas');
            $table->date('tanggal_pelepasan')->nullable()->after('aktif');
            $table->string('metode_pelepasan')->nullable()->after('tanggal_pelepasan')
                ->comment('Dijual, Dihapusbukukan (Rusak Total), Hilang');
            $table->text('catatan_pelepasan')->nullable()->after('metode_pelepasan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commodities', function (Blueprint $table) {
            $table->dropColumn(['aktif', 'tanggal_pelepasan', 'metode_pelepasan', 'catatan_pelepasan']);
        });
    }
};
