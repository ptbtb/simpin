<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_code', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('code_type_id')->nullable();
            $table->integer('normal_balance_id')->nullable();
            $table->integer('code_category_id')->nullable();
            $table->integer('sumber_dana_id')->nullable();
            $table->string('induk_id', 12)->nullable();
            $table->string('CODE', 12)->nullable();
            $table->tinyInteger('is_parent')->default(0);
            $table->string('NAMA_TRANSAKSI', 45)->nullable();
            $table->string('u_entry', 50)->nullable();
            $table->integer('active')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
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
        Schema::dropIfExists('t_code');
    }
}
