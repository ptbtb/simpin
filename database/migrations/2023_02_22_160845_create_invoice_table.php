<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice', function (Blueprint $table) {
            $table->integer('id')->autoIncrement();
            $table->integer('invoice_type_id')->nullable();
            $table->string('invoice_number', 50);
            $table->integer('kode_anggota');
            $table->string('description', 190)->nullable();
            $table->integer('amount')->nullable();
            $table->integer('discount')->nullable();
            $table->integer('tax')->nullable();
            $table->integer('final_amount')->nullable();
            $table->dateTime('date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->dateTime('paid_date')->nullable();
            $table->tinyInteger('invoice_status_id');
            $table->integer('version')->nullable();
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
        Schema::dropIfExists('invoice');
    }
}
