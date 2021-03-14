<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('api', ['except' => ['login','register', 'update']]);
    }

    function register (Request $request){
        $validate = Validator::make($request->all(),[
            'name' => 'required',
            'type' => 'required|in:SuperUser,Ketua,Panitera,KPA,Panmud,PP,Jurusita,PPK,Bendahara,Pengelola Persediaan',    
            'fullname' => 'required',
            'password' => 'required|min:6',
        ]);

        if ($validate->fails()) {
            return response()->json([
                'error_code' => 1,
                'error_message' => $validate->errors()->all()//'You must be add all fields'
            ]);
        }

        $request['password_verified'] = Crypt::encrypt($request['password']);
        $request['password'] = Hash::make($request['password']);
        $request['log'] = '0';
        $user = User::create($request->toArray());
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = $user->id . '_' . $file->getClientOriginalName();
            $path = $file->move(public_path('images'), $filename);
            $user->update(['avatar' => $filename]);
        }

        return $this->responseSuccess($user);
    }

    function login (Request $request){
        $validate = Validator::make($request->all(),[
            'name' => 'required',
            'password' => 'required'
        ]);
        if ($validate->fails()) {
            return response()->json([
                'error_code' => 1,
                'error_message' => 'You must be add all fields'
            ]);
        }

        if (! $token = Auth::attempt($validate->validated())) {
            return response()->json([
                'error_code' => 1,
                'error_message' => 'Authorization'
            ]);
        }

        $user = Auth::user()::where('name', $request->name)->first();
        if ($user->log) {
            return response()->json([
                'error_code' => 1,
                'error_message' => 'You must logout in another device first !'
            ]);
        }
        $user->log = '1';
        $user->update();
        return $this->responseWithToken($token);
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
        $user = User::with('getPerkaraRelation')->get();
        return response()->json([
            'error_code' => '0',
            'error_message' => '',
            'data' => $user
        ]);
    }

    public function logout()
    {
        $us = Auth::user();
        $user = User::where('name', $us->name)->first();
        $user->update(['log' => '0']);
        Auth::logout();
        return response()->json([
                'error_code' => 0,
                'error_message' => 'You successfully logout !'
            ]);
    }

    public function update(Request $request){

        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required',
            'fullname' => 'required'
        ]);

        if ($validate->fails()) {
            return response([
                'error_code' => '1',
                'error_message' => 'fields don`t empty if you want update users information'
            ]);
        }

        $db = Auth::user();
        $user = User::where('name', $db->name)->first();
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = $db->id . '_' . $file->getClientOriginalName();

            if ($user->avatar) {
                $file_loc = public_path('images/') . $user->avatar;
                unlink($file_loc);
            }
            $path = $file->move(public_path('images'), $filename);
            $user->avatar = $request->avatar = $filename;
        }
        if (!is_null($request->password)) $user->password_verified = Crypt::encrypt($request->password);
        if (!is_null($request->fullname)) $user->fullname = $request->fullname;
        if (!is_null($request->password)) $user->password = Hash::make($request->password);
        if (!is_null($request->name)) $user->name = $request->name;
        if (!is_null($request->type)) $user->type = $request->type;
        $user->update();
        return $this->responseSuccess($user);
    }

    public function me(){
        $db = Auth::user();
        $user = User::where('name', $db->name)->first();
        return $this->responseSuccess($user);
    }

    public function delete()
    {
        $db = Auth::user();
        $user = User::where('name', $db->name)->first();
        if (!$user){
            return response()->json([
                'error_code' => '1',
                'error_message' => 'Users not found!',
            ]);
        }
        $file_loc = public_path('images/') . $user->avatar;
        unlink($file_loc);
        $user->delete();
        return response()->json([
            'error_code' => '0',
            'error_message' => 'Successfully delete users!',
        ]);
    }

    public function responseWithToken($token){
        return response()->json([
            'error_code' => 0,
            'error_message' => '',
            'token' => $token
        ]);
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

