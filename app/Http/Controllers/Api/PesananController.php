<?php

namespace App\Http\Controllers\Api;

use App\detailpesanan;
use App\Http\Controllers\Controller;
use App\pesanan;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PesananController extends Controller
{
    public function index(){
//        $pesanan = pesanan::all();
        $pesanan = DB::table('pesanan')
            ->join('reservasi','pesanan.id_reservasi','=','reservasi.id_reservasi')
            ->join( 'meja','reservasi.id_meja','=','meja.id_meja')
            ->select('pesanan.*','reservasi.*','meja.*')
            ->get();
        if(count($pesanan)>0){
            return response([
                'message'=>'Retrive All Success',
                'data'=> $pesanan
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function getpesananbyIdReservasi(Request $request){
//        $detailpesanan = detailpesanan::all();
        $request = $request->all();
        $pesanan = DB::table('pesanan')
            ->where('pesanan.id_reservasi','=',$request['id_reservasi'])
            ->get();
        if(count($pesanan)>0){
            return response([
                'message'=>'Retrive All Success',
                'data'=> $pesanan
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function show($id){
        $pesanan = pesanan::find($id);

        if(!is_null($pesanan)){
            return response([
                'message'=>'Retrive pesanan Success',
                'data'=> $pesanan
            ],200);
        }

        return response([
            'message'=>'pesanan Not Found',
            'data'=>null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_reservasi'=>'required|numeric',
            'tanggal_pesanan'=>'required|date',
            'waktu_pesanan'=>'required|regex:/(\d+\:\d+\:\d+)/',
            'status_pesanan'=>'required'
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()],460);

        $pesanan = pesanan::create($storeData);
        return response([
            'message' => 'Add pesanan Success',
            'data' => $pesanan,
        ],200);
    }

    public function destroy($id){
        $pesanan=pesanan::find($id);

        if(is_null($pesanan)){
            return response([
                'message'=>'pesanan Not Found',
                'data'=>null,
            ],400);
        }
        $detailpesanan = detailpesanan::
            where('id_pesanan','=',$pesanan->id_pesanan)->first();

        if(!is_null($detailpesanan)){
            if($detailpesanan->status_detailpesanan == 'Selesai'){
                $detailpesanan->delete();
            }else{
                return response([
                    'message' => 'Pesanan Ada yang belum Selesai',
                    'data' => $detailpesanan,
                ], 200);
            }
        }
        if($pesanan->delete()){
            return response([
                'message'=>'Delete pesanan Success',
                'data'=>$pesanan,
            ],200);
        }

        return response([
            'message'=>'Delete pesanan Failed',
            'data'=>null,
        ],400);
    }

    public function update(Request $request,$id){
        $pesanan = pesanan::find($id);
        if(is_null($pesanan)){
            return response([
                'message'=>'pesanan not Found',
                'data'=>null,
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'id_reservasi'=>'required|numeric',
            'tanggal_pesanan'=>'required|date',
            'waktu_pesanan'=>'required|regex:/(\d+\:\d+\:\d+)/',
            'status_pesanan'=>'required'
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $pesanan->id_reservasi = $updateData['id_reservasi'];
        $pesanan->tanggal_pesanan = $updateData['tanggal_pesanan'];
        $pesanan->waktu_pesanan = $updateData['waktu_pesanan'];
        $pesanan->status_pesanan = $updateData['status_pesanan'];

        if ($pesanan->save()) {
            return response([
                'message' => 'Update pesanan Success',
                'data' => $pesanan,
            ], 200);
        }
        return response([
            'message' => 'Update pesanan Failed',
            'data' => null,
        ], 400);
    }
}
