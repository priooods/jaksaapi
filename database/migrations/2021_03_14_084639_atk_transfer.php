<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AtkTransfer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('atk_transfer', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->int('jumlah');
            $table->int('request_id')->nullable();
            $table->int('verified_id')->nullable();
            $table->int('acquired_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('atk_transfer');
    }
}
