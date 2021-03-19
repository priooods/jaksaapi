<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PerkaraTable extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('perkaras', function (Blueprint $table) {
            $table->id();
            $table->string('tanggal');
            $table->string('nomor');
            $table->string('jenis');
            $table->string('identitas');
            $table->string('dakwaan');
            $table->string('penahanan');
            $table->unsignedBigInteger('pp')->nullable();
            $table->unsignedBigInteger('jurusita')->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
        Schema::table('perkaras', function($table)
        {
            $table->foreign('pp')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('jurusita')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('perkaras');
    }
}
