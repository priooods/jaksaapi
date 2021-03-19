<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtkRequest extends Model
{
    use HasFactory;
    public $timestamps = false;
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
        'acquired_id'
    ];
    public function atk_transfer(){
        return $this->hasMany(ATKTransfer::class,'request_id');
    }
    // public function atk_item(){
    //     return $this->hasManyThrough(ATKTransfer::class,ATK::class);
    // }
}