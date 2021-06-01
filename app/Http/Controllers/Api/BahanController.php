<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use App\bahan;

class BahanController extends Controller
{
    public function index(){
        $bahan = bahan::all();
        $stokbahan = DB::table('stokbahan')->get();
//        if(count($bahan)>0){
//            for($x=0;$x<count($bahan);$x++){
//                if(count($stokbahan)>0){
//                    for ($y=0;$y<count($stokbahan);$y++){
//                        if($bahan[$x]->id_bahan == $stokbahan[$y]->id_bahan && $stokbahan[$y]->status_stok == 'Siap Restok'){
//                            DB::table('bahan')->where('id_bahan','=',$bahan[$x]->id_bahan)
//                                ->update(['sisa_bahan'=>$stokbahan[$y]->jmlh_beli + $bahan[$x]->sisa_bahan]);
//
//                            DB::table('stokbahan')->where('id_stokBahan','=',$stokbahan[$y]->id_stokBahan)
//                                ->update(['status_stok'=>'Sudah Restok']);
//                        }
//                    }
//                }
//            }
//            $bahan = bahan::all();
//            if(count($bahan)>0){
//                return response([
//                    'message'=>'Retrive All Success',
//                    'data'=> $bahan
//                ],200);
//            }
//        }

        $bahan = bahan::all();
        if(count($bahan)>0){
            return response([
                'message'=>'Retrive All Success',
                'data'=> $bahan
            ],200);
        }
        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function show($id){
        $bahan = bahan::find($id);

        if(!is_null($bahan)){
            return response([
                'message'=>'Retrive Bahan Success',
                'data'=> $bahan
            ],200);
        }

        return response([
            'message'=>'Bahan Not Found',
            'data'=>null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'unit_bahan'=>'required',
            'sisa_bahan'=>'required|numeric',
            'serving_size'=>'required|numeric',
            'nama_bahan'=>'required',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()],460);

        $bahan = bahan::create($storeData);
        return response([
            'message' => 'Add Bahan Success',
            'data' => $bahan,
        ],200);
    }

    public function destroy($id){
        $bahan=bahan::find($id);

        if(is_null($bahan)){
            return response([
                'message'=>'Bahan Not Found',
                'data'=>null,
            ],400);
        }

        if($bahan->delete()){
            return response([
                'message'=>'Delete Bahan Success',
                'data'=>$bahan,
            ],200);
        }

        return response([
            'message'=>'Delete Bahan Failed',
            'data'=>null,
        ],400);
    }

    public function update(Request $request,$id){
        $bahan = bahan::find($id);
        if(is_null($bahan)){
            return response([
                'message'=>'Bahan not Found',
                'data'=>null,
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'unit_bahan'=>'required',
            'sisa_bahan'=>'required|numeric',
            'serving_size'=>'required|numeric',
            'nama_bahan'=>'required',
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $bahan->unit_bahan = $updateData['unit_bahan'];
        $bahan->sisa_bahan = $updateData['sisa_bahan'];
        $bahan->serving_size = $updateData['serving_size'];
        $bahan->nama_bahan = $updateData['nama_bahan'];

        if ($bahan->save()) {
            return response([
                'message' => 'Update Bahan Success',
                'data' => $bahan,
            ], 200);
        }
        return response([
            'message' => 'Update Bahan Failed',
            'data' => null,
        ], 400);
    }
}
