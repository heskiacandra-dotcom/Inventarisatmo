<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemNumberToCommoditiesTable extends Migration
{
    public function up()
    {
        Schema::table('commodities', function (Blueprint $table) {
            // Tambah kolom item_number
            $table->unsignedInteger('item_number')->after('commodity_location_id');
            
            // Tambah unique constraint kombinasi location dan nomor urut
            $table->unique(['commodity_location_id', 'item_number']);
        });
    }

    public function down()
    {
        Schema::table('commodities', function (Blueprint $table) {
            $table->dropUnique(['commodity_location_id_item_number_unique']);
            $table->dropColumn('item_number');
        });
    }
}