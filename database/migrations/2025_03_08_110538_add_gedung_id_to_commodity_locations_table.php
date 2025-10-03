<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGedungIdToCommodityLocationsTable extends Migration
{
    public function up()
{
    Schema::table('commodity_locations', function (Blueprint $table) {
        // Tambahkan kolom sebagai nullable
        $table->unsignedBigInteger('gedung_id')->nullable()->after('id');
        
        // Tambahkan foreign key
        $table->foreign('gedung_id')
              ->references('id')
              ->on('gedung')
              ->onDelete('cascade');
    });
}

    public function down()
    {
        Schema::table('commodity_locations', function (Blueprint $table) {
            $table->dropForeign(['gedung_id']);
            $table->dropColumn('gedung_id');
        });
    }
};
