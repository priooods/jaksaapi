<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function resSuccess($data){
        return response()->json([
            'error_code' => 0,
            'data' => $data
        ]);
    }
    public function resFailed($code,$error){
        return response()->json([
            'error_code' => $code,
            'error_message' => $error
        ]);
    }
    public function validing($request,$items){
        $validate = Validator::make($request,$items);
        if ($validate->fails()) {
            return $this->resFailed(1,$validate->errors()->all());
        }else
            return null;
    }
    public function unlink_file($name){
        if ($name==null)
            return;
        try{
            $file_loc = public_path("files\\") . $name;
            unlink($file_loc);
        }catch(\Throwable $th){}
    }
}
