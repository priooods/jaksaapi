<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class Pembayaran extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_id');
            $table->unsignedBigInteger('ppk_id');
            $table->string('surat');
            $table->string('kuitansi')->nullable();
            $table->timestamp('created')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
        Schema::table('pembayarans', function($table)
        {
            $table->foreign('ppk_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('surat_id')->references('id')->on('surat_tugas')->onDelete('RESTRICT');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('pembayarans');
    }
}
