<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTNotifikasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_notifikasi', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('role_id');
            $table->string('receiver', 50);
            $table->string('peminjam', 50)->nullable();
            $table->string('informasi_notifikasi', 100);
            $table->tinyInteger('has_read');
            $table->string('keterangan', 190);
            $table->string('url', 190);
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
        Schema::dropIfExists('t_notifikasi');
    }
}
