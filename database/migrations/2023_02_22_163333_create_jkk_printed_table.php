<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJkkPrintedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jkk_printed', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('jkk_printed_type_id')->nullable();
            $table->string('jkk_number', 50)->nullable();
            $table->dateTime('printed_at')->nullable();
            $table->integer('printed_by')->nullable();
            $table->string('payment_confirmation_path', 500)->nullable();
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
        Schema::dropIfExists('jkk_printed');
    }
}
