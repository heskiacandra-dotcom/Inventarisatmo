<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // database/migrations/xxxx_xx_xx_xxxxxx_add_penyusutan_fields_to_commodities_table.php
public function up()
{
    Schema::table('commodities', function (Blueprint $table) {
        $table->date('tanggal_perolehan')->nullable(); // kapan aset dibeli
        $table->integer('umur_manfaat')->default(60); // bulan (misal 60 bulan = 5 tahun)
        $table->decimal('nilai_residu', 15, 2)->default(0); // nilai sisa
    });
}

public function down()
{
    Schema::table('commodities', function (Blueprint $table) {
        $table->dropColumn(['tanggal_perolehan', 'umur_manfaat', 'nilai_residu']);
    });
}

};
