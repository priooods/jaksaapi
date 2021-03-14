<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Perkara;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class PerkaraController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('api', ['except' => []]);
    }

    public function all(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'token' => 'required',
        ]);
        if ($validate->fails()) {
            return response([
                'error_code' => 1,
                'error_message' => 'Your token not found !'
            ]);
        }
        return response()->json([
            'error_code' => '0',
            'error_message' => '',
            'data' => Perkara::all()
        ]);
    }
    function add(Request $request){
        $validate = Validator::make($request->all(),[
            'tanggal' => 'required|date',
            'nomor' => 'required',    
            'jenis' => 'required',
            'identitas' => 'required',
            'dakwaan' => 'required',
            'penahanan' => 'required',
            'token' => 'required'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'error_code' => 1,
                'error_message' => $validate->errors()->all()//'You must be add all fields'
            ]);
        }

        $request->panitera = Auth::user()->id;
        $perkara = Perkara::create($request->toArray());

        return $this->responseSuccess($perkara);
    }

    public function update(Request $request){

        $validate = Validator::make($request->all(), [
            'id' => 'required}int',
            'tanggal' => 'required|date',
            'nomor' => 'required',
            'jenis' => 'required',
            'identitas' => 'required',
            'dakwaan' => 'required',
            'penahanan' => 'required'
        ]);

        if ($validate->fails()) {
            return response([
                'error_code' => '1',
                'error_message' => 'fields don`t empty if you want update perkaras information'
            ]);
        }

        $perkara = Perkara::where('id', $db->id)->first();
        
        if (!is_null($request->tanggal)) $perkara->tanggal = $request->tanggal;
        if (!is_null($request->nomor)) $perkara->nomor = $request->nomor;
        if (!is_null($request->jenis)) $perkara->jenis = $request->jenis;
        if (!is_null($request->identitas)) $perkara->identitas = $request->identitas;
        if (!is_null($request->dakwaan)) $perkara->dakwaan = $request->dakwaan;
        if (!is_null($request->penahanan)) $perkara->penahanan = $request->penahanan;
        $perkara->update();
        return $this->responseSuccess($perkara);
    }

    public function responseSuccess($data){
        return response()->json([
            'error_code' => 0,
            'error_message' => '',
            'data' => [
                'id' => $data->id,
                'tanggal' => $data->tanggal,
                'nomor' => $data->nomor,
                'jenis' => $data->jenis,
                'identitas' => $data->identitas,
                'dakwaan' => $data->dakwaan,
                'penahanan' => $data->penahanan
            ],
        ]);
    }
}
