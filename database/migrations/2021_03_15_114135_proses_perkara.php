<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ProsesPerkara extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('proses_perkaras', function (Blueprint $table) {
            $table->id();
            $table->string('hari');
            $table->date('tanggal');
            $table->string('agenda');
            $table->unsignedBigInteger('perkara_id');
            // $table->unsignedBigInteger('request_id')->nullable();
            $table->timestamp('created')->default(DB::raw('CURRENT_TIMESTAMP'));
        });
        Schema::table('proses_perkaras', function($table)
        {
            // $table->foreign('request_id')->references('id')->on('atk_requests')->onDelete('restrict');
            $table->foreign('perkara_id')->references('id')->on('perkaras')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('proses_perkaras');
    }
}
