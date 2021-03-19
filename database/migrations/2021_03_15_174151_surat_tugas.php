<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SuratTugas extends Migration{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('surat_tugas', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe',[
                'Pengiriman penetapan hari sidang',
                'Pengiriman petikan / salinan putusan',
                'Pengiriman surat penahanan & perpanjangan penahanan',
                'Pengiriman salinan putusan',
                'Pemberitahuan proses banding',
                'Pemberitahuan putusan banding',
                'Pemberitahuan proses kasasi',
                'Pemberitahuan putusan kasasi']);
            $table->string('surat_tugas');
            $table->string('daftar_pengantar')->nullable();
            $table->unsignedBigInteger('perkara_id')->nullable();
            $table->unsignedBigInteger('verifier_id')->nullable();
        });
        Schema::table('surat_tugas', function($table)
        {
            $table->foreign('perkara_id')->references('id')->on('perkaras')->onDelete('cascade');
            $table->foreign('verifier_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        Schema::dropIfExists('surat_tugass');
    }
}
