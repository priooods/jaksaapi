<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Perkara;
use App\Models\ProsesPerkara;
use App\Models\SuratTugas;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PerkaraController extends Controller
{
    #region CRUD Perkara
    public function all(Request $request){
        if ($request->id == null)
            return Perkara::all();
        else
            return Perkara::find($request->id);
    }
    public function create(Request $request){
        if ($validate = $this->validing($request->all(),[
            'tanggal' => 'required|date',
            'nomor' => 'required',    
            'jenis' => 'required',
            'identitas' => 'required',
            'dakwaan' => 'required',
            'penahanan' => 'required',
            'pp' => 'required|int',
            'jurusita' => 'required|int',
            'token' => 'required'
        ]))
            return $validate;

        try {
            $perkara = Perkara::create($request->toArray());
        } catch (\Throwable $th) {
            return $this->resFailed('1',"perkara failed to create! pay attention again to the PP & Jurusita id!");
        }
        return $this->resSuccess("perkara successfully created!");
    }

    public function update(Request $request){
        if ($validate = $this->validing($request->all(),['id' => 'required|int']))
            return $validate;
        try {
            $perkara = Perkara::find($request->id)->update($request->all());
            return $this->resSuccess("perkara successfully updated!");
        } catch (\Throwable $th) {
            return $this->resFailed("1","perkara failed to update! pay attention again to the PP & Jurusita id!");
        }
    }
    public function delete(Request $request){
        if ($validate = $this->validing($request->all(),['id' => 'required|int']))
            return $validate;
        try {
            $perkara = Perkara::find($request->id)->delete();
            return $this->resSuccess("perkara successfully daleted!");
        } catch (\Throwable $th) {
            return $this->resFailed("perkara failed to delete!");
        }
    }
    #endregion
    #region Proses Perkara
    public function pp_input(Request $request){
        if ($validate = $this->validing($request->all(),[
            'tanggal' => 'required|date',
            'hari' => 'required',    
            'agenda' => 'required',
            'perkara_id' => 'required',
            'token' => 'required'
        ]))
            return $validate;
            
        $user = Auth::user()->id;
        $perkara = Perkara::find($request->perkara_id);
        if ($perkara == null)
            return $this->resFailed('2','perkara with id = '.$request->perkara_id.' is not found!');
        if (Perkara::find($request->perkara_id)->pp != $user)
            return $this->resFailed('3',"panitera pengganti can't access this data perkara");
        $perkara = ProsesPerkara::create($request->toArray());
        // $list = array();
        // foreach ($perkara as $obj) {
        //     array_push($list, $obj->tanggal.'/No.'.$obj->nomor.'/'.$obj->jenis.'/'.$obj->identitas);
        // }
        // if ($perkara == null)
        //     return $this->resSuccess([]);

        return $this->resSuccess("proses perkara successfully created!");
    }
    public function pp_show(Request $request){
        if ($request->id==null)
            return $this->resSuccess(ProsesPerkara::all());
        else{
            $pp = ProsesPerkara::find($request->id);
            if ($pp == null)
                return $this->resFailed(1,"proses perkara not found!");
            $pp->perkara;
            return $this->resSuccess($pp);
        }
    }
    public function pp_delete(Request $request){
        if ($validate = $this->validing($request->all(),['id' => 'required|int']))
            return $validate;
        try {
            $surattugas = ProsesPerkara::find($request->id)->delete();
            return $this->resSuccess("proses perkara tugas successfully daleted!");
        } catch (\Throwable $th) {
            return $this->resFailed("1","proses perkara failed to delete!");
        }
    }
    public function pp_update(Request $request){
        if ($validate = $this->validing($request->all(),['id' => 'required|int']))
            return $validate;
        try {
            $perkara = ProsesPerkara::find($request->id)->update($request->all());
            return $this->resSuccess("proses perkara successfully updated!");
        } catch (\Throwable $th) {
            return $this->resFailed("1","proses perkara failed to update! pay attention again to the tanggal or id!");
        }
    }
    #endregion
    #region Perkara
    public function pp_perkara(Request $request){
        if ($validate = $this->validing($request->all(),['token' => 'required']))
            return $validate;

        $user = Auth::user()->id;
        $perkara = Perkara::where("pp",$user)->select('id','tanggal','nomor','jenis','identitas')->get();
        // $list = array();
        // foreach ($perkara as $obj) {
        //     array_push($list, $obj->tanggal.'/No.'.$obj->nomor.'/'.$obj->jenis.'/'.$obj->identitas);
        // }
        // if ($perkara == null)
        //     return $this->resSuccess([]);

        return $this->resSuccess($perkara);
    }

    public function jurusita_perkara(Request $request){
        if ($validate = $this->validing($request->all(),[
            'token' => 'required'
        ]))
            return $validate;
        
        $user = Auth::user()->id;
        $daftar = Perkara::where('jurusita',$user)->select('id','tanggal','nomor','jenis','identitas')->get();
        return $this->resSuccess($daftar);
    }
    public function jurusita_all(Request $request){
        if ($validate = $this->validing($request->all(),[
            'token' => 'required'
        ]))
            return $validate;
        
        $user = Auth::user()->id;
        $surat = array();
        $daftar = Perkara::where('jurusita',$user)->with('my_surat')->get();
        foreach ($daftar as $daft) {
            $surat = array_merge($surat,$daft->my_surat->toArray());
        }
        return $this->resSuccess($surat);
    }
    #endregion
    #region Surat Perkara
    public function panmud_surat(Request $request){
        if ($validate = $this->validing($request->all(),[
            'tipe' => 'required',
            'surat' => 'required',    
            'perkara_id' => 'required',
            'token' => 'required'
        ]))
            return $validate;

        $user = Auth::user()->id;
        $perkara = Perkara::find($request->perkara_id);
        if ($perkara == null)
            return $this->resFailed('2','perkara with id = '.$request->perkara_id.' is not found!');
        if ($request->hasFile('surat')) {
            $file = $request->file('surat');
            $statement = DB::select("SHOW TABLE STATUS LIKE 'surat_tugas'");
            $filename = $request->perkara_id. '_tugas'.($statement[0]->Auto_increment) . '_' . $user .'_'.$file->getClientOriginalName();
            $path = $file->move(public_path('files'), $filename);
            $request['surat_tugas'] = $filename;
        }else
            return $this->resFailed("3","to create Surat Tugas need surat (file format) field!");

        SuratTugas::create($request->toArray());
        
        return $this->resSuccess("surat tugas successfully created!");
    }
    public function all_surat(Request $request){
        if ($request->id == null)
            return $this->resSuccess(SuratTugas::all());
        
        $surat = SuratTugas::find($request->id);
        if ($surat == null)
            return $this->resFailed(1,"surat tugas not found!");
        $surat->perkara;
        return $this->resSuccess($surat);
    }
    public function update_surat(Request $request){
        if ($validate = $this->validing($request->all(),['token'=>'required','id' => 'required|int']))
            return $validate;
        
        $surattugas = SuratTugas::find($request->id);
        $user = Auth::user()->id;
        if ($surattugas == null)
            return $this->resFailed('2','surat tugas with id = '.$request->id.' is not found!');
        if ($surattugas->verifier_id != null)
            return $this->resFailed('3','surat tugas already verified and cannot be modified!');

        try {
            if ($request->surat!=null){
                if ($request->hasFile('surat')) {
                    $file = $request->file('surat');
                    $filename = $surattugas->perkara_id. '_tugas'.$surattugas->id . '_' . $user .'_'.$file->getClientOriginalName();
                    $this->unlink_file($surattugas->surat_tugas);
                    $path = $file->move(public_path('files'), $filename);
                    $request['surat_tugas'] = $filename;
                }else
                    return $this->resFailed("3","to update Surat Tugas, surat must be in file format!");
            }
            if ($request->bukti!=null){
                if ($request->hasFile('bukti')) {
                    $file = $request->file('bukti');
                    $filename = $surattugas->perkara_id. '_bukti'.$surattugas->id . '_' . $user .'_'.$file->getClientOriginalName();
                    $this->unlink_file($surattugas->surat_tugas);
                    $path = $file->move(public_path('files'), $filename);
                    $request['daftar_pengantar'] = $filename;
                }else
                    return $this->resFailed("3","to update Bukti Tugas, surat must be in file format!");
            }
            $surattugas = $surattugas->update($request->all());
            return $this->resSuccess("surat tugas successfully updated!");
        } catch (\Throwable $th) {
            return $this->resFailed('1',"surat tugas failed to update! pay attention again to tipe or surat!");
        }
    }
    public function delete_surat(Request $request){
        if ($validate = $this->validing($request->all(),['id' => 'required|int']))
            return $validate;
        try {
            $surattugas = SuratTugas::find($request->id);
            $this->unlink_file($surattugas->surat_tugas);
            $this->unlink_file($surattugas->daftar_pengantar);
            $surattugas->delete();
            return $this->resSuccess("surat tugas successfully daleted!");
        } catch (\Throwable $th) {
            return $this->resFailed("surat tugas failed to delete!");
        }
    }

    public function jurusita_notif(Request $request){
        if ($validate = $this->validing($request->all(),[
            'token' => 'required'
        ]))
            return $validate;
        
        $user = Auth::user()->id;
        $surat = array();
        $daftar = Perkara::where('jurusita',$user)->with('my_surat', function ($query) {
            return $query->where('daftar_pengantar', null);
        })->get();
        foreach ($daftar as $daft) {
            $surat = array_merge($surat,$daft->my_surat->toArray());
        }
        return $this->resSuccess($surat);
    }
    public function jurusita_surat(Request $request){
        if ($validate = $this->validing($request->all(),[
            'surat' => 'required',
            'id' => 'required',
            'token' => 'required'
        ]))
            return $validate;
        
        $surattugas = SuratTugas::find($request->id);
        $user = Auth::user()->id;
        if ($surattugas == null)
            return $this->resFailed('2','surat tugas with id = '.$request->id.' is not found!');
        try {
            if ($request->hasFile('surat')) {
                $file = $request->file('surat');
                $filename = $surattugas->perkara_id. '_bukti'.$surattugas->id . '_' . $user .'_'.$file->getClientOriginalName();
                              
                $this->unlink_file($surattugas->surat);
                $path = $file->move(public_path('files'), $filename);
                $request['daftar_pengantar'] = $filename;
            }else
                return $this->resFailed("3","to update Surat Tugas, surat must be in file format!");

            $surattugas = $surattugas->update($request->all());
            return $this->resSuccess("surat tugas successfully updated!");
        } catch (\Throwable $th) {
            return $this->resFailed('1',"surat tugas failed to update! pay attention again to surat!");
        }
    }
    public function ppk_notif(Request $request){
        if ($validate = $this->validing($request->all(),[
            'token' => 'required'
        ]))
            return $validate;
        
        $surat = SuratTugas::where("daftar_pengantar",'!=',null)->where('verifier_id',null)->get();
        return $this->resSuccess($surat);
    }
    public function acc_surat(Request $request){
        if ($validate = $this->validing($request->all(),[
            'id' => 'required',
            'token' => 'required'
        ]))
            return $validate;
        
        $surat = SuratTugas::find($request->id);
        $user = Auth::user()->id;
        if ($surat->daftar_pengantar == null && $surat->surat_tugas)
            return $this->resFailed('2',"surat tugas not completed yet!");
        
        $surat->update(["verifier_id"=>$user]);
        return $this->resSuccess("surat tugas verified completely!");
    }
    public function ppk_surat(Request $request){
        if ($validate = $this->validing($request->all(),[
            'token' => 'required'
        ]))
            return $validate;
        $user = Auth::user();
        if ($user == null)
            return $this->resFailed(1,'user not found!');
        $surat = SuratTugas::where('verifier_id',$user->id)->get();
        return $this->resSuccess($surat);
    }
    #endregion
}
