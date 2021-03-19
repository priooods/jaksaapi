<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AtkTransfer extends Model
{
    use HasFactory;//, Notifiable;
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'barang_id',
        'jumlah',
        'request_id'
    ];

    // public function atk_name(){
    //     $name = $this->hasOne(ATK::class,'id','barang_id');
    //     return $name->select('name');
    // }
}
