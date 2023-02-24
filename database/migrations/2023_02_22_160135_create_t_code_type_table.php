<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTCodeTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_code_type', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('code_group_id')->nullable();
            $table->string('name', 50)->nullable();
            $table->string('short_name', 10)->nullable();
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
        Schema::dropIfExists('t_code_type');
    }
}
