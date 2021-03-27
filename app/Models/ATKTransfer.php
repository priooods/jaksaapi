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

    /**
     * Get the user associated with the AtkTransfer
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function request()
    {
        return $this->belongsTo(AtkRequest::class, 'request_id');
    }

    // public function atk_name(){
    //     $name = $this->hasOne(ATK::class,'id','barang_id');
    //     return $name->select('name');
    // }
}
