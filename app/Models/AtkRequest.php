<?php

namespace App\Models;

use GuzzleHttp\Handler\Proxy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtkRequest extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $hidden =[
        'proses_id'
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pp_id',
        'ppk_id',
        'log_id',
        'penyerahan',
        'acquired_id',
        'proses_id'
    ];
    public function atk_transfer(){
        return $this->hasMany(ATKTransfer::class,'request_id');
    }
    /**
     * Get the user associated with the AtkRequest
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function proses()
    {
        return $this->belongsTo(ProsesPerkara::class, 'proses_id');
    }
    // public function atk_item(){
    //     return $this->hasManyThrough(ATKTransfer::class,ATK::class);
    // }
}
