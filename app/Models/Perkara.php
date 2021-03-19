<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Model;


class Perkara extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'tanggal',
        'nomor',
        'jenis',
        'identitas',
        'dakwaan',
        'penahanan',
        'panitera',
        'pp',
        'jurusita'
    ];
    public function my_surat(){
        return $this->hasMany(SuratTugas::class,'perkara_id');
    }
}
