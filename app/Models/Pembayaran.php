<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ppk_id',
        'surat_id',
        'surat',
        'kuitansi'
    ];

    /**
     * Get the user associated with the Pembayaran
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function surat_tugas()
    {
        return $this->hasOne(SuratTugas::class, 'id', 'surat_id');
    }
}
