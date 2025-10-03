<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKodeToGedungTable extends Migration
{
    public function up()
    {
        Schema::table('gedung', function (Blueprint $table) {
            $table->string('kode', 5)->nullable()->after('id'); // VARCHAR(5)
        });
    }

    public function down()
    {
        Schema::table('gedung', function (Blueprint $table) {
            $table->dropColumn('kode');
        });
    }
};