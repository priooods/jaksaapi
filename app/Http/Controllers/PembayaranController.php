<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    #region CURD Pembayaran
    public function show(Request $request){
        if ($request->id == null)
            return $this->resSuccess(Pembayaran::with('surat_tugas')->get());
        else
            return $this->resSuccess(Pembayaran::find($request->id)->with('surat_tugas'));
    }
    public function create(Request $request){
        if ($validate = $this->validing($request->all(),[
            'bayar' => 'required',
            'token' => 'required',
            'surat_id' => 'required'
        ]))
            return $validate;

        try {
            $user = Auth::user()->id;
            $request['ppk_id'] = $user;
            if ($request->hasFile('bayar')) {
                $file = $request->file('bayar');
                $statement = DB::select("SHOW TABLE STATUS LIKE 'pembayarans'");
                $filename = 'surat_bayar_'.($statement[0]->Auto_increment) . '_' . $user .'_'.$file->getClientOriginalName();
                $path = $file->move(public_path('files'), $filename);
                $request['surat'] = $filename;
            }else
                return $this->resFailed("3","to create Pembayaran need bayar (file format) field!");

            Pembayaran::create($request->toArray());
        } catch (\Throwable $th) {
            return $this->resFailed('1',"failed to create pembayaran! pay attention again to the bayar!");
        }
        return $this->resSuccess("pembayaran successfully created!");
    }
    public function update(Request $request){
        if ($validate = $this->validing($request->all(),['token'=>'required','id' => 'required|int']))
            return $validate;

        $pembayaran = Pembayaran::find($request->id);
        if ($pembayaran == null)
            return $this->resFailed('2','pembayaran with id = '.$request->id.' is not found!');
        if ($pembayaran->kuitansi != null)
            return $this->resFailed('3','pembayaran already paid and cannot be modified!');

        try {
            $user = Auth::user()->id;
            $request['ppk_id'] = $user;
            if ($request->bayar!=null){
                if ($request->hasFile('bayar')) {
                    $file = $request->file('bayar');
                    $filename = 'surat_bayar_'.$pembayaran->id . '_' . $user .'_'.$file->getClientOriginalName();
                    $this->unlink_file($pembayaran->surat);
                    $path = $file->move(public_path('files'), $filename);
                    $request['surat'] = $filename;
                }else
                    return $this->resFailed("3","to update Pembayaran, bayar must be in file format!");
            }
            $pembayaran = $pembayaran->update($request->all());
            return $this->resSuccess("pembayaran successfully updated!");
        } catch (\Throwable $th) {
            return $this->resFailed('1',"pembayaran failed to update! pay attention again to bayar!");
        }
    }
    public function delete(Request $request){
        if ($validate = $this->validing($request->all(),['id' => 'required|int']))
            return $validate;
        try {
            $pembayaran = Pembayaran::find($request->id);
            $this->unlink_file($pembayaran->surat);
            $pembayaran->delete();
            return $this->resSuccess("pembayaran successfully daleted!");
        } catch (\Throwable $th) {
            return $this->resFailed("1","pembayaran failed to delete!");
        }
    }
    #endregion

    #region BENDAHARA
    public function bayar_notif(){
        return $this->resSuccess(Pembayaran::where('kuitansi',null)->get());
    }
    public function kuitansi(Request $request){
        if ($validate = $this->validing($request->all(),[
            'token'=>'required',
            'id' => 'required|int',
            'bukti' => 'required'
        ]))
            return $validate;

        $pembayaran = Pembayaran::find($request->id);
        if ($pembayaran == null)
            return $this->resFailed('2','pembayaran with id = '.$request->id.' is not found!');

        try {
            $user = Auth::user()->id;
            if ($request->bukti!=null){
                if ($request->hasFile('bukti')) {
                    $file = $request->file('bukti');
                    $filename = 'kuitansi_'.$pembayaran->id . '_' . $user .'_'.$file->getClientOriginalName();
                    $this->unlink_file($pembayaran->surat);
                    $path = $file->move(public_path('files'), $filename);
                    $request['kuitansi'] = $filename;
                }else
                    return $this->resFailed("3","to update Kuitansi, bukti must be in file format!");
            }
            $pembayaran = $pembayaran->update($request->all());
            return $this->resSuccess("kuitansi pembayaran successfully updated!");
        } catch (\Throwable $th) {
            return $this->resFailed('1',"kuitansi pembayaran failed to update! pay attention again to bukti!");
        }
    }
    #endregion

    #region LAPORAN PEMBAYARAN
        public function laporan(){
            return $this->resSuccess(Pembayaran::where('kuitansi','!=',null)->with('surat_tugas')->get());
        }
    #endregion
}
