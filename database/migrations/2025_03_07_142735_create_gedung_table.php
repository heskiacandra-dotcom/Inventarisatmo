<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGedungTable extends Migration
{
    public function up()
    {
        Schema::create('gedung', function (Blueprint $table) {
            $table->id(); // Kolam id (auto increment, primary key)
            $table->string('name'); // Kolom name
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('gedung');
    }
};
