<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ATKTransfer extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'jumlah'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'request_id',
        'verified_id',
        'acquired_id'
    ];

    public function getRequestRelation(){
        return $this->hasMany(User::class,'request_id','id');
    }
    public function getVerfiedRelation(){
        return $this->hasMany(User::class,'verified_id','id');
    }
    public function getAcquiredRelation(){
        return $this->hasMany(User::class,'acquired_id','id');
    }
}
