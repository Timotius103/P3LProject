<?php

namespace App\Http\Controllers\Api;

use App\bahan;
use App\detailpesanan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\stokbahan;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isNull;

class StokBahanController extends Controller
{
    public function index(){
//        $stokbahan = stokbahan::all();
        $stokbahan = DB::table('stokbahan')
            ->join('bahan','stokbahan.id_bahan','=','bahan.id_bahan')
            ->select('stokbahan.*','bahan.*')
            ->get();

//        $detailpesanan = DB::table('detailpesanan')
////            ->join('menu', 'detailpesanan.id_menu', '=','menu.id_menu' )
////            ->join('bahan', 'bahan.id_bahan', '=', 'menu.id_bahan')
////            ->join('stokbahan', 'stokbahan.id_bahan', '=','bahan.id_bahan')
////            ->where(['id_bahan' => $stokbahan[$x]->id_bahan])
//            ->get(['detailpesanan.*']);
//
//        $bahan = DB::table('bahan')
////            ->where(['id_bahan'=>$stokbahan[$x]->id_bahan])
////            ->select('bahan.*')
//            ->get();
//        $menu = DB::table('menu')->get();
//        if(count($stokbahan)>0){
//            for($x = 0; $x<count($stokbahan); $x++) {
//                if(count($detailpesanan)>0){
//                    for($y = 0;$y<count($detailpesanan);$y++){
//                        if(count($menu)>0){
//                            $total_item = 0;
//                            for ($z=0;$z<count($menu);$z++){
//                                if(count($bahan)>0){
//                                    for($a=0;$a<count($bahan);$a++){
//                                        if($detailpesanan[$y]->id_menu == $menu[$z]->id_menu && $menu[$z]->id_bahan == $bahan[$a]->id_bahan
//                                            && $bahan[$a]->id_bahan == $stokbahan[$x]->id_bahan){
//
//                                            $total_item = $total_item + $detailpesanan[$y]->jmlh_pesanan ;
//                                            $totalStok = $bahan[$a]->serving_size * $total_item;
//
//                                            DB::table('stokbahan')->where(['id_bahan'=>$stokbahan[$x]->id_bahan])
//                                                ->update(['jmlh_buang'=>$totalStok]);
//                                        }
//                                    }
//                                }
//                            }
//                        }
//                    }
//                }
//            }
//            return response([
//                'message'=>'Retrive All Success',
//                'data'=> $stokbahan
//            ],200);
//
//        }



//        $bahan = Bahan::where('id_bahan', '=', $menu->id_bahan)->firstOrFail();


        if(count($stokbahan)>0){
            return response([
                'message'=>'Retrive All Success',
                'data'=> $stokbahan
            ],200);
        }



        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function show($id){
        $stokbahan = stokbahan::find($id);

        if(!is_null($stokbahan)){
            return response([
                'message'=>'Retrive Stok Bahan Success',
                'data'=> $stokbahan
            ],200);
        }

        return response([
            'message'=>'Stok Bahan Not Found',
            'data'=>null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_bahan'=>'required|numeric|exists:bahan,id_bahan',
            'jmlh_beli'=>'required|numeric',
            'tgl_kadaluarsa'=>'required|date',
            'harga_beli'=>'required|numeric',
            'tgl_beli'=>'required|date'
        ]);

        $storeData['jmlh_buang'] = 0;

        if($validate->fails())
            return response(['message' => $validate->errors()],460);


        $bahan=bahan::where('id_bahan','=',$storeData['id_bahan'])
            ->first();
        if(!is_null($bahan))
            $bahan->sisa_bahan = $bahan->sisa_bahan + $storeData['jmlh_beli'];
            if($bahan->sisa_bahan > $bahan->serving_size){
                $storeData['status_stok']='Ready Stok';
            }else{
                $storeData['status_stok']='Stok Kosong';
            }

        $stokbahan = stokbahan::create($storeData);
        if($bahan->save()){
            return response([
                'message' => 'Add Stok Bahan Success',
                'data' => $stokbahan,
            ],200);
        }
    }

    public function destroy($id){
        $stokbahan=stokbahan::find($id);

        if(is_null($stokbahan)){
            return response([
                'message'=>'Stok Bahan Not Found',
                'data'=>null,
            ],400);
        }

        if($stokbahan->delete()){
            return response([
                'message'=>'Delete Stok Bahan Success',
                'data'=>$stokbahan,
            ],200);
        }

        return response([
            'message'=>'Delete Stok Bahan Failed',
            'data'=>null,
        ],400);
    }

    public function update(Request $request,$id){
        $stokbahan = stokbahan::find($id);
        if(is_null($stokbahan)){
            return response([
                'message'=>'Stok Bahan not Found',
                'data'=>null,
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'id_bahan'=>'required|numeric|exists:bahan,id_bahan',
            'jmlh_beli'=>'required|numeric',
            'tgl_kadaluarsa'=>'required|date',
            'harga_beli'=>'required|numeric',
            'jmlh_buang'=>'numeric',
            'tgl_beli'=>'required|date'
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);


        $bahan=bahan::where('id_bahan','=',$stokbahan['id_bahan'])
            ->first();
        if(!is_null($bahan))
            $stokbahan->jmlh_beli = $updateData['jmlh_beli'] - $stokbahan->jmlh_beli;
            $stokjmlhbaru = $updateData['jmlh_beli'];
            $bahan->sisa_bahan = $bahan->sisa_bahan + $stokbahan->jmlh_beli;
            if($bahan->sisa_bahan > $bahan->serving_size){
                $stokbahan->status_stok='Ready Stok';
            }else{
                $stokbahan->status_stok='Stok Kosong';
            }

        $stokbahan->id_bahan = $updateData['id_bahan'];
        $stokbahan->jmlh_beli = $stokjmlhbaru;
        $stokbahan->tgl_kadaluarsa = $updateData['tgl_kadaluarsa'];
        $stokbahan->harga_beli = $updateData['harga_beli'];
        $stokbahan->tgl_beli = $updateData['tgl_beli'];
        $updateData['jmlh_buang'] = 0;
        $stokbahan->jmlh_buang = $updateData['jmlh_buang'];



        if ($stokbahan->save()) {
            if($bahan->save()){
                return response([
                    'message' => 'Update Stok Bahan Success',
                    'data' => $stokbahan,
                ], 200);
            }
        }
        return response([
            'message' => 'Update Stok Bahan Failed',
            'data' => null,
        ], 400);
    }
}
