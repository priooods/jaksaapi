<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratTugas extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'tipe',
        'surat_tugas',
        'daftar_pengantar',
        'perkara_id',
        'verifier_id'
    ];
    public function perkara(){
        return $this->belongsTo(perkara::class, 'perkara_id', 'id');
    }
    public function pembayaran(){
        return $this->hasOne(Pembayaran::class,'surat_id');
    }
}
