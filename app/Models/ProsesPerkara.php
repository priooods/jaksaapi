<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProsesPerkara extends Model
{
    use HasFactory;
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'hari',
        'tanggal',
        'agenda',
        'perkara_id'
    ];

    public function perkara(){
        return $this->belongsTo(perkara::class, 'perkara_id', 'id');
    }
    public function request(){
        return $this->hasOne(AtkRequest::class, 'proses_id');
    }
}
