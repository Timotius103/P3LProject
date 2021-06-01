<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use App\meja;

class MejaController extends Controller
{
    public function index(){
        $meja = meja::all();
        if(count($meja)>0){
            return response([
                'message'=>'Retrive All Success',
                'data'=> $meja
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function cekMejaTersedia(){
//        $meja = meja::all();
        $meja = DB::table('meja')
            ->where('status_meja','=','Kosong')
            ->get();
//        $meja = DB::table('meja')->get();
//        if(count($meja)>0){
//            for($x=0;$x<count($meja);$x++){
//                if($meja[$x]->status_meja == 'Kosong'){
//                    DB::table('meja')
//                    ->where('id_meja','=','Kosong')
//                    ->get();
//
//                }
//            }
//            $meja = DB::table('meja')->get();
//            return response([
//                'message'=>'Retrive All Success',
//                'data'=> $meja
//            ],200);
//        }
        if(count($meja)>0){
            return response([
                'message'=>'Retrive All Success',
                'data'=> $meja
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function show($id){
        $meja = meja::find($id);

        if(!is_null($meja)){
            return response([
                'message'=>'Retrive Meja Success',
                'data'=> $meja
            ],200);
        }

        return response([
            'message'=>'Meja Not Found',
            'data'=>null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'status_meja'=>'required',
            'nomor_meja'=>'required|numeric',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()],460);

        $meja = meja::create($storeData);
        return response([
            'message' => 'Add Meja Success',
            'data' => $meja,
        ],200);
    }

    public function destroy($id){
        $meja=meja::find($id);

        if(is_null($meja)){
            return response([
                'message'=>'Meja Not Found',
                'data'=>null,
            ],400);
        }

        if($meja->delete()){
            return response([
                'message'=>'Delete Meja Success',
                'data'=>$meja,
            ],200);
        }

        return response([
            'message'=>'Delete Meja Failed',
            'data'=>null,
        ],400);
    }

    public function update(Request $request,$id){
        $meja = meja::find($id);
        if(is_null($meja)){
            return response([
                'message'=>'Meja not Found',
                'data'=>null,
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'status_meja'=>'required',
            'nomor_meja'=>'required|numeric',
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $meja->status_meja = $updateData['status_meja'];
        $meja->nomor_meja = $updateData['nomor_meja'];

        if ($meja->save()) {
            return response([
                'message' => 'Update Meja Success',
                'data' => $meja,
            ], 200);
        }
        return response([
            'message' => 'Update Meja Failed',
            'data' => null,
        ], 400);
    }
}
