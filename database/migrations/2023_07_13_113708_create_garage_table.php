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
        Schema::create('garage', function (Blueprint $table) {
            $table->id();
            $table->date('date_report');
            $table->date('date_start');
            $table->date('date_end');
            $table->string('reason');
            $table->boolean('status');

            $table->foreignId('registration_id')->nullable();
            $table->foreign('registration_id')->references('id')->on('truck_fleets')->onUpdate('cascade')->onDelete('cascade');

            $table->foreignId('breakdowns_id')->nullable();
            $table->foreign('breakdowns_id')->references('id')->on('breakdowns')->onUpdate('cascade')->onDelete('cascade');

            $table->foreignId('garage_status_id')->nullable();
            $table->foreign('garage_status_id')->references('id')->on('garage_status')->onUpdate('cascade')->onDelete('cascade');
            
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
        Schema::dropIfExists('garage');
    }
};
