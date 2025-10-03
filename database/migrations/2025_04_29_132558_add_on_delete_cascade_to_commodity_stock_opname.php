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
        Schema::table('commodity_stock_opname', function (Blueprint $table) {
            // Hapus foreign key yang lama
            $table->dropForeign(['commodity_id']);
    
            // Buat foreign key baru dengan ON DELETE CASCADE
            $table->foreign('commodity_id')
                  ->references('id')
                  ->on('commodities')
                  ->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::table('commodity_stock_opname', function (Blueprint $table) {
            $table->dropForeign(['commodity_id']);
            
            // Kembalikan ke foreign key tanpa cascade (opsional)
            $table->foreign('commodity_id')
                  ->references('id')
                  ->on('commodities');
        });
    }
};
