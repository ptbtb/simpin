<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTPenghasilanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_penghasilan', function (Blueprint $table) {
            $table->id();
            $table->integer('kode_anggota')->nullable();
            $table->integer('id_jenis_penghasilan')->nullable();
            $table->integer('value')->nullable()->default(0);
            $table->string('file_path', 190)->nullable()->default('');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_penghasilan');
    }
}
