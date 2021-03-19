<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AtkRequest extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('atk_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pp_id');
            $table->unsignedBigInteger('ppk_id')->nullable();
            $table->unsignedBigInteger('log_id')->nullable();
            $table->string('penyerahan')->nullable();
            $table->string('penerimaan')->nullable();
        });
        
        Schema::table('atk_requests', function($table)
        {
            $table->foreign('pp_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ppk_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('log_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('atk_requests');
    }
}
