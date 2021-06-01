<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\detailpesanan;
use App\menu;
use App\stokbahan;
use App\bahan;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailPesananController extends Controller
{
    public function index(){
//        $detailpesanan = detailpesanan::all();
        $detailpesanan = DB::table('detailpesanan')
            ->join('menu','detailpesanan.id_menu','=','menu.id_menu')
            ->select('detailpesanan.*','menu.*')
            ->get();
        if(count($detailpesanan)>0){
            return response([
                'message'=>'Retrive All Success',
                'data'=> $detailpesanan
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function getpesananbyId(Request $request){
//        $detailpesanan = detailpesanan::all();
        $request = $request->all();
        $detailpesanan = DB::table('detailpesanan')
            ->join('menu','detailpesanan.id_menu','=','menu.id_menu')
            ->select('detailpesanan.*','menu.*')
            ->where('detailpesanan.id_pesanan','=',$request['id_pesanan'])
            ->get();
        if(count($detailpesanan)>0){
            return response([
                'message'=>'Retrive All Success',
                'data'=> $detailpesanan
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }




    public function show($id){
        $detailpesanan = detailpesanan::find($id);

        if(!is_null($detailpesanan)){
            return response([
                'message'=>'Retrive detailpesanan Success',
                'data'=> $detailpesanan
            ],200);
        }

        return response([
            'message'=>'detailpesanan Not Found',
            'data'=>null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_pesanan'=>'required|numeric',
            'id_menu'=>'required|numeric',
            'urutan_pesanan'=>'required|numeric',
            'jmlh_pesanan'=>'required|numeric',
            'status_detailpesanan'=>'required',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()],460);
        $menu = menu::where('id_menu', '=', $storeData['id_menu'])->first();
//        $bahan = DB::table('bahan')
//            ->where('id_bahan', '=', $menu->id_bahan)->first();
        $bahan = bahan::where('id_bahan', '=', $menu->id_bahan)->first();
        $stokBahan = stokbahan::where('id_bahan', '=', $bahan->id_bahan)->first();

        if(!is_null($stokBahan))
            $stokBahan->jmlh_buang += $storeData['jmlh_pesanan'] * $bahan->serving_size;
            if($bahan->sisa_bahan >= $bahan->serving_size){
                $bahan->sisa_bahan=$bahan->sisa_bahan-$storeData['jmlh_pesanan'];
                if($bahan->sisa_bahan < $bahan->serving_size){
                    $stokBahan->status_stok='Stok Kosong';
                    $menu->status_menu = 'Tidak Tersedia';
//                    if($bahan->sisa_bahan < 0){
//                        $bahan->sisa_bahan = 0;
//                        $stokBahan->status_stok='Stok Kosong';
//                    }
                }
            }
            else{
                return response([
                    'message'=>'Mohon maaf menu yang anda pesan telah habis',
                    'data'=>null
                ],404);
            }

        $detailpesanan = detailpesanan::create($storeData);
        if(!is_null($stokBahan))
            if($menu->save()){
                if($stokBahan->save()) {
                    if($bahan->save()){
                        return response([
                            'message' => 'Add detailpesanan Success',
                            'data' => $detailpesanan,
                        ], 200);
                    }
                }
            }

        return response([
            'message' => 'Add detailpesanan Success',
            'data' => $detailpesanan,
        ], 200);
    }

    public function destroy($id){
        $detailpesanan=detailpesanan::find($id);

        if(is_null($detailpesanan)){
            return response([
                'message'=>'detailpesanan Not Found',
                'data'=>null,
            ],400);
        }
        $menu = DB::table('menu')
            ->where('id_menu', '=', $detailpesanan->id_menu)
            ->select('menu.*')->first();
        $bahan = bahan::where('id_bahan', '=', $menu->id_bahan)->select('bahan.*')->first();
        $stokBahan = stokbahan::where('id_bahan', '=', $bahan->id_bahan)->first();

        if(!is_null($stokBahan))
//            jumlah buang += jumlah pesan * serving size
            if($detailpesanan->status_detailpesanan != 'Selesai'){
                $bahan->sisa_bahan=$bahan->sisa_bahan+$detailpesanan->jmlh_pesanan;
                $stokBahan->jmlh_buang -= $detailpesanan->jmlh_pesanan*$bahan->serving_size;
                if($stokBahan->jmlh_buang<0){
                    $stokBahan=0;
                }
            }

        if($stokBahan->save()){
            if($bahan->save()){
                if($detailpesanan->delete()){
                    return response([
                        'message'=>'Delete detailpesanan Success',
                        'data'=>$detailpesanan,
                    ],200);
                }
            }
        }

        return response([
            'message'=>'Delete detailpesanan Failed',
            'data'=>null,
        ],400);
    }

    public function update(Request $request,$id){
        $detailpesanan = detailpesanan::find($id);
        if(is_null($detailpesanan)){
            return response([
                'message'=>'detailpesanan not Found',
                'data'=>null,
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'id_pesanan'=>'required|numeric',
            'id_menu'=>'required|numeric',
            'urutan_pesanan'=>'required|numeric',
            'jmlh_pesanan'=>'required|numeric',
            'status_detailpesanan'=>'required'
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $menu = DB::table('menu')
            ->where('id_menu', '=', $detailpesanan['id_menu'])->first();
//        $bahan = DB::table('bahan')
//            ->where('id_bahan', '=', $menu->id_bahan)->first();
        $bahan = bahan::where('id_bahan', '=', $menu->id_bahan)->first();
        $stokBahan = stokbahan::where('id_bahan', '=', $bahan->id_bahan)->first();

        if(!is_null($stokBahan))
            $bahan->sisa_bahan=$bahan->sisa_bahan+$detailpesanan->jmlh_pesanan;
            $bahan->sisa_bahan=$bahan->sisa_bahan-$updateData['jmlh_pesanan'];
            $stokBahan->jmlh_buang = 0;
            $stokBahan->jmlh_buang += $updateData['jmlh_pesanan'] * $bahan->serving_size;
            $jmlhpesananbaru = $updateData['jmlh_pesanan'];
            if($bahan->sisa_bahan < $bahan->serving_size){
                $stokBahan->status_stok='Stok Kosong';
            }else{
                $stokBahan->status_stok='Ready Stok';
            }
        if($updateData['status_detailpesanan']=='Selesai')
            $updateData['urutan_pesanan']=0;

        $detailpesanan->id_pesanan = $updateData['id_pesanan'];
        $detailpesanan->id_menu = $updateData['id_menu'];
        $detailpesanan->urutan_pesanan = $updateData['urutan_pesanan'];
//        $detailpesanan->jmlh_pesanan = $updateData['jmlh_pesanan'];
        $detailpesanan->jmlh_pesanan = $jmlhpesananbaru;
        $detailpesanan->status_detailpesanan = $updateData['status_detailpesanan'];

        if ($detailpesanan->save()) {
            if($stokBahan->save()){
                if($bahan->save()){
                    return response([
                        'message' => 'Update detailpesanan Success',
                        'data' => $detailpesanan,
                    ], 200);
                }
            }
        }
        return response([
            'message' => 'Update detailpesanan Failed',
            'data' => null,
        ], 400);
    }
}
