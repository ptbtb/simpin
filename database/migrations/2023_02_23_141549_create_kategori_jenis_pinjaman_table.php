<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKategoriJenisPinjamanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kategori_jenis_pinjaman', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->nullable();
            $table->integer('biaya_admin')->nullable();
            $table->integer('provisi')->nullable()->comment('persentase');
            $table->double('jasa')->nullable();
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
        Schema::dropIfExists('kategori_jenis_pinjaman');
    }
}
