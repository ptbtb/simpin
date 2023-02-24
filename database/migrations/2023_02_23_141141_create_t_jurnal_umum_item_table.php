<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTJurnalUmumItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_jurnal_umum_item', function (Blueprint $table) {
            $table->id();
            $table->integer('jurnal_umum_id')->nullable();
            $table->integer('code_id')->nullable();
            $table->integer('normal_balance_id')->nullable();
            $table->double('nominal')->nullable();
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
        Schema::dropIfExists('t_jurnal_umum_item');
    }
}
