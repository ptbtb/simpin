<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('kode_anggota')->nullable();
            $table->string('name', 190);
            $table->string('email', 190)->unique();
            $table->string('password', 190);
            $table->rememberToken();
            $table->string('photo_profile_path', 100)->nullable();
            $table->string('activation_code', 190)->nullable();
            $table->tinyInteger('is_verified')->default(0);
            $table->dateTime('email_verified_at')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('is_allow')->default(1)->nullable();
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
        Schema::dropIfExists('users');
    }
}
