<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AtkTransfer extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('atk_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('barang_id')->nullable();
            $table->integer('jumlah')->nullable();
            $table->unsignedBigInteger('request_id')->nullable();
        });
        
        Schema::table('atk_transfers', function($table)
        {
            $table->foreign('barang_id')->references('id')->on('atks')->onDelete('cascade');
            $table->foreign('request_id')->references('id')->on('atk_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('atk_transfers');
    }
}
