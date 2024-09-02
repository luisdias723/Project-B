+<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->default('/storage/avatars/1653993172_user.png');

            $table->integer('vac_days')->nullable();

            $table->string('mobile_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('type', 30)->nullable();

            $table->string('birthday')->nullable();
            $table->string('address')->nullable();
            $table->string('zipcode', 50)->nullable();
            $table->string('city')->nullable();
            $table->string('country', 50)->nullable();

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
        Schema::dropIfExists('users');
    }
};
