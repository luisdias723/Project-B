<?php

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
        Schema::create('truck_fleets', function (Blueprint $table) {
            $table->id();
            $table->string('registration');
            $table->foreignId('model_id');
            $table->foreign('model_id')->references('id')->on('truck_models')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('type_id');
            $table->foreign('type_id')->references('id')->on('transport_types')->onUpdate('cascade')->onDelete('cascade');
            $table->year('year');
            $table->date('inspection_date');
            $table->float('max_cargo');
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
        Schema::dropIfExists('truck_fleets');
    }
};
