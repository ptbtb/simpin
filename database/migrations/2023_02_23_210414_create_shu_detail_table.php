<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShuDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shu_detail', function (Blueprint $table) {
            $table->id();
            $table->integer('shu_id');
            $table->integer('shu_detail_type_id');
            $table->integer('month')->nullable();
            $table->string('pokok', 150)->nullable();
            $table->string('wajib', 150)->nullable();
            $table->string('sukarela', 150)->nullable();
            $table->string('saldo_pws', 150)->nullable();
            $table->string('shu_pws', 150)->nullable();
            $table->string('saldo_khusus', 150)->nullable();
            $table->string('shu_khusus', 150)->nullable();
            $table->string('cashback', 150)->nullable();
            $table->string('kontribusi', 150)->nullable();
            $table->string('total_shu_sebelum_pajak', 150)->nullable();
            $table->string('pajak_pph', 150)->nullable()->comment('pph1');
            $table->string('total_shu_setelah_pajak', 150)->nullable();
            $table->string('shu_disimpan', 150)->nullable()->comment('25%');
            $table->string('shu_dibagi', 150)->nullable('75%');
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
        Schema::dropIfExists('shu_detail');
    }
}
