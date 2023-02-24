<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJenisPenghasilanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jenis_penghasilan', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('company_group_id')->nullable();
            $table->string('name', 249)->nullable();
            $table->string('rule_name', 50)->nullable();
            $table->tinyInteger('is_visible')->default(0);
            $table->integer('sequence')->nullable();
            $table->tinyInteger('is_penghasilan_tertentu')->default(0);
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
        Schema::dropIfExists('jenis_penghasilan');
    }
}
