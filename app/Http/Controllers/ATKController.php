<?php

namespace App\Http\Controllers;

use App\Models\Atk;
use App\Models\AtkRequest;
use App\Models\AtkTransfer;
use Illuminate\Auth\Events\Verified;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class AtkController extends Controller
{
    // public function __construct(){
    //     $this->middleware('api', ['except' => ['login','register', 'update']]);
    // }

#region CURD ATK
    function add(Request $request){
        if ($validate = $this->validing($request->all(),[
            'name' => 'required',
            'jumlah' => 'required|int'
        ]))
            return $validate;

        $atk = Atk::where('name', $request->name)->first();
        if ($atk == null)
            $atk = Atk::create($request->toArray());
        else{
            $atk->jumlah += $request->jumlah;
            $atk->save();
        }

        return $this->resSuccess($atk);
    }

    function get(Request $request){
        if ($validate = $this->validing($request->all(),[
            'name' => 'required',
            'jumlah' => 'required|int'
        ]))
            return $validate;
            
        $atk = Atk::where('name', $request->name)->first();
        if ($atk == null)
            return $this->resFailed(2,$request->name." not found!");
        else
        if ($atk->jumlah<$request->jumlah)
            return $this->resFailed(3,$request->name." have ".$atk->jumlah." item left!");
        else{
            $atk->jumlah -= $request->jumlah;
            $atk->save();
        }

        return $this->resSuccess($atk);
    }

    function update(Request $request){
        if ($validate = $this->validing($request->all(),['id' => 'required|int']))
            return $validate;
            
        $atk = Atk::find($request->id);
        if ($atk == null)
            return $this->resFailed(2,"item with id = ".$request->id." is not found!");
        else{
            if (!is_null($request->name)) $atk->name = $request->name;
            if (!is_null($request->jumlah)) $atk->jumlah = $request->jumlah;
            if (!is_null($request->keterangan)) $atk->keterangan = $request->keterangan;
        }
        $atk->update();
        return $this->responseSuccess($atk);
    }

    function delete(Request $request){
        if ($validate = $this->validing($request->all(),['id' => 'required|int']))
            return $validate;
            
        $atk = Atk::find($request->id);
        if ($atk == null)
            return $this->resFailed(2,"barang with id = ".$request->id." is not found!");
        else
            $atk->delete();
        return $this->resSuccess($atk->name." is deleted!");
    }

    function show(Request $request){
        if ($request->id == null)
            return ATK::all();
        else
            return Atk::find($request->id);
    }
    #endregion

#region REQUEST ATK (PP)
        function request(Request $request){
            $barang = $request->barang;
            $user = Auth::user();
            for ($i=0; $i < count($barang); $i++) { 
                if (isset($barang[$i])){
                    if ($validate = $this->validing($barang[$i],[
                        'barang_id' => 'required|int',
                        'jumlah' => 'required|int',
                    ]))
                        return $validate;
                        
                    $barang_id = $barang[$i]['barang_id'];
                    if (ATK::find($barang_id) == null)
                        return $this->resFailed('1','id barang['.$i.'] = '.$barang_id.' is not found!');
                }else
                    return $this->resFailed('1','barang with array = '.$i.' is not defined!');
            }
            $atkreq = ATKRequest::create(["pp_id"=>$user->id]);
            $b = $atkreq->atk_transfer()->createMany($barang);
            return $this->resSuccess('request atk successfully created!');
        }
        function show_request(Request $request){
            if ($validate = $this->validing($request->all(),['id' => 'required|int']))
                return $validate;

            $atkreq = ATKRequest::find($request->id);
            if ($atkreq == null)
                return $this->resFailed(2,'Request ATK with id = '.$request->id.' is not found!');

            $atkreq->barang = $this->detail_item($request->id);
            return $this->resSuccess($atkreq);
        }
        function my_request(Request $request){
            if ($validate = $this->validing($request->all(),['token' => 'required']))
                return $validate;

            $user = Auth::user()->id;
            $atkreq = AtkRequest::where('pp_id',$user)->get();
            for ($i=0; $i < count($atkreq); $i++) { 
                $atkreq[$i]->barang = $this->detail_item($atkreq[$i]->id);
            }
            return $this->resSuccess($atkreq);
        }
        function delete_request(Request $request){
            if ($validate = $this->validing($request->all(),['token' => 'required','id'=>'required|int']))
                return $validate;

            $atkreq = AtkRequest::find($request->id);
            if ($atkreq == null)
                return $this->resFailed(2,'Request ATK with id = '.$request->id.' is not found!');
            if ($atkreq->log_id != null)
                return $this->resFailed(3,"You cannot delete this request at all! Penyedia Persediaan has or is executing it!");
            
            $atkreq->delete();
            return $this->resSuccess("request successfully deleted!");
        }

        function pp_notif(Request $request){
            $atkreq = DB::table('atk_requests')->where('atk_requests.penerimaan', null)->where('atk_requests.log_id','!=',null)->get();
            for ($i=0; $i < count($atkreq); $i++) { 
                $atkreq[$i]->barang = $this->detail_item($atkreq[$i]->id);
            }
            return $this->resSuccess($atkreq);
        }

        function pp_acc(Request $request){
            if ($validate = $this->validing($request->all(),[
                'id' => 'required|int',
                'token' => 'required',
                'penerimaan' => 'required'
            ]))
                return $validate;

            $user = Auth::user();
            if ($user == null)
                return $this->resFailed('1','you are not logged in correctly!');
                
            $atkreq = ATKRequest::find($request->id);
            if ($user->id != $atkreq->pp_id)
                return $this->resFailed('1','you dont have permission to this data!');
                
            if ($request->hasFile('penerimaan')) {
                $file = $request->file('penerimaan');
                $filename = $atkreq->id . '_' . $user->id . '_terima'.$file->getClientOriginalName();

                if ($atkreq->penerimaan!=null) {
                    $file_loc = public_path("files\\") . $atkreq->penerimaan;
                    unlink($file_loc);
                }
                $path = $file->move(public_path('files'), $filename);
                $atkreq->penerimaan = $request->penerimaan = $filename;
            }
            $atkreq->save();
            return $this->resSuccess($atkreq);
        }
    #endregion

#region REQUEST ATK (PPK)
        function ppk_notif(Request $request){
            if ($validate = $this->validing($request->all(),['token'=>'required']))
                return $validate;
            $atkreq = AtkRequest::where('ppk_id',null)->get();
            // $atkreq = DB::table('atk_requests')->where('atk_requests.ppk_id', null)->get();
            for ($i=0; $i < count($atkreq); $i++) { 
                $atkreq[$i]->barang = $this->detail_item($atkreq[$i]->id);
            }
            return $this->resSuccess($atkreq);
        }

        function ppk_acc(Request $request){
            if ($validate = $this->validing($request->all(),[
                'id' => 'required|int',
                'token' => 'required'
            ]))
                return $validate;
            $user = Auth::user();
            if ($user == null)
                return $this->resFailed('1','you are not logged in correctly!');
            else
            if ($user->type != 'PPK')
                return $this->resFailed('3','only PPK can verified this request! [your title = '.$user->type.']');
            
            $atkreq = ATKRequest::find($request->id);
            if ($atkreq == null)
                return $this->resFailed(2,'Request ATK with id = '.$request->id.' is not found!');
            $atkreq->ppk_id = $user->id;
            $atkreq->save();
            // $atkreq->barang = 
            return $this->resSuccess($atkreq);
        }
        function ppk_show(Request $request){
            if ($validate = $this->validing($request->all(),[
                'token' => 'required'
            ]))
                return $validate;
            $user = Auth::user();
            if ($user == null)
                return $this->resFailed(1,"user not found!");
            return $this->resSuccess(ATKRequest::where("ppk_id",$user->id)->get());
        }
#endregion
#region REQUEST ATK (LOG)
        function log_notif(Request $request){
            $atkreq = DB::table('atk_requests')->where('atk_requests.log_id', null)->where('atk_requests.ppk_id','!=',null)->get();
            for ($i=0; $i < count($atkreq); $i++) { 
                $atkreq[$i]->barang = $this->detail_item($atkreq[$i]->id);
            }
            return $this->resSuccess($atkreq);
        }
        function log_acc(Request $request){
            if ($validate = $this->validing($request->all(),[
                'id' => 'required|int',
                'token' => 'required',
                'penyerahan' => 'required'
            ]))
                return $validate;

            $user = Auth::user();
            if ($user == null)
                return $this->resFailed('1','you are not logged in correctly!');
            else
            if ($user->type != 'Pengelola Persediaan')
                return $this->resFailed('3','only Pengelola Persediaan can verified this request! [your title = '.$user->type.']');
                
            $atkreq = ATKRequest::find($request->id);
            if ($atkreq == null)
                return $this->resFailed(2,'Request ATK with id = '.$request->id.' is not found!');
            if ($atkreq->log_id>0 && $atkreq->log_id!=$user->id)
                return $this->resFailed('1','you dont have permission to this data!');

            if ($request->hasFile('penyerahan')) {
                $file = $request->file('penyerahan');
                $filename = $atkreq->id . '_' . $user->id . '_serah'.$file->getClientOriginalName();

                if ($atkreq->penyerahan!=null) {
                    $file_loc = public_path("files\\") . $atkreq->penyerahan;
                    unlink($file_loc);
                }
                $path = $file->move(public_path('files'), $filename);
                $atkreq->penyerahan = $request->penyerahan = $filename;
            }
            $atkreq->log_id = $user->id;
            $atkreq->save();
            
            return $this->resSuccess($atkreq);
        }
        function log_show(Request $request){
            if ($validate = $this->validing($request->all(),[
                'token' => 'required'
            ]))
                return $validate;
            $user = Auth::user();
            if ($user == null)
                return $this->resFailed(1,"user not found!");
            return $this->resSuccess(ATKRequest::where("log_id",$user->id)->get());
        }
#endregion

        public function detail_item($id){
            return DB::table('atk_transfers')->
                where('request_id',$id)->
                join('atks','atks.id','=','atk_transfers.barang_id')->
                select('atk_transfers.jumlah','atks.id','name')->
                get();
        }
}
