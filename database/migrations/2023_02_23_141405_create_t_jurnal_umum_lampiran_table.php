<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTJurnalUmumLampiranTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_jurnal_umum_lampiran', function (Blueprint $table) {
            $table->id();
            $table->integer('jurnal_umum_id')->nullable();
            $table->sttring('lampiran', 190)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_jurnal_umum_lampiran');
    }
}
