<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Tabel Peminjaman Barang
        Schema::create('item_lendings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commodity_id')->constrained('commodities')->onDelete('cascade');
            $table->string('user_name'); // Nama Peminjam
            $table->unsignedBigInteger('origin_location_id'); // Lokasi Asal
            $table->unsignedBigInteger('return_location_id')->nullable(); // Lokasi Kembali
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->date('actual_return_date')->nullable();
            $table->time('actual_return_time')->nullable();
            $table->string('status')->default('borrowed'); // 'borrowed' atau 'returned'
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('origin_location_id')->references('id')->on('commodity_locations');
            $table->foreign('return_location_id')->references('id')->on('commodity_locations');
        });

        // 2. Tabel Peminjaman Ruangan
        Schema::create('room_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('commodity_location_id'); // ID Ruangan
            $table->string('user_name');
            $table->string('purpose'); // Kegunaan
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('status')->default('booked'); 
            $table->timestamps();

            $table->foreign('commodity_location_id')->references('id')->on('commodity_locations')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('room_bookings');
        Schema::dropIfExists('item_lendings');
    }
};