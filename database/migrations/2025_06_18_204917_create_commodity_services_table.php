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
    Schema::create('commodity_services', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('commodity_id'); // relasi ke tabel barang
        $table->date('tanggal_diservis');
        $table->string('kerusakan', 300);
        $table->string('perbaikan', 500);
        $table->date('tanggal_selesai')->nullable();
        $table->enum('status', ['Sedang Diservis', 'Selesai'])->default('Sedang Diservis');
        $table->timestamps();

        $table->foreign('commodity_id')->references('id')->on('commodities')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commodity_services');
    }
};
