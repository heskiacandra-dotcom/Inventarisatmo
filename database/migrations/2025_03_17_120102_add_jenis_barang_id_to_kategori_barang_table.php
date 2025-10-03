<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('kategori_barang', function (Blueprint $table) {
            $table->unsignedBigInteger('jenis_barang_id')->nullable();
            $table->foreign('jenis_barang_id')->references('id')->on('jenis_barang')->onDelete('set null');
        });
    }
    
    public function down()
    {
        Schema::table('kategori_barang', function (Blueprint $table) {
            $table->dropForeign(['jenis_barang_id']);
            $table->dropColumn('jenis_barang_id');
        });
    }
};
