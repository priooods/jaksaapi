<?php

namespace App\Http\Controllers;

use App\Models\ATK;
use App\Models\ATKTransfer;
use Illuminate\Http\Request;

class ATKController extends Controller
{
    public function __construct()
    {
        $this->middleware('api', ['except' => ['login','register', 'update']]);
    }
    function add(Request $request){
        $validate = Validator::make($request->all(),[
            'name' => 'required|date',
            'jumlah' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'error_code' => 1,
                'error_message' => $validate->errors()->all()//'You must be add all fields'
            ]);
        }
        $atk = Perkara::create($request->toArray());

        return $this->responseSuccess($atk);
    }
    public function responseSuccess($data){
        return response()->json([
            'error_code' => 0,
            'error_message' => '',
            'data' => [
                'id' => $data->id,
                'name' => $data->name,
                'fullname' => $data->fullname,
                'password' => Crypt::decrypt($data->password_verified),
                'type' => $data->type,
                'avatar' => $data->avatar,
                'log' => $data->log
            ],
        ]);
    }

}
