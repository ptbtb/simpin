<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTKelasCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_kelas_company', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->nullable();
            $table->integer('id_jenis_anggota')->nullable();
            $table->string('nama', 50)->nullable();
            $table->integer('sequence')->nullable();
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
        Schema::dropIfExists('t_kelas_company');
    }
}
