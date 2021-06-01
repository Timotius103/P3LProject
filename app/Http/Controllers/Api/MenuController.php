<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\menu;
use Illuminate\Support\Facades\DB;


class MenuController extends Controller
{
    public function index(){
//        $menu = menu::all();
        $menu = DB::table('menu')
            ->join('bahan','menu.id_bahan','=','bahan.id_bahan')
            ->select('menu.*','bahan.*')
            ->get();
        $bahan = DB::table('bahan')->get();
        if(count($menu)>0){
            for($x= 0;$x<count($menu);$x++ ){
                if(count($bahan)>0){
                    for($y=0;$y<count($bahan);$y++){
                        if($menu[$x]->id_bahan == $bahan[$y]->id_bahan && $bahan[$y]->sisa_bahan < $bahan[$y]->serving_size){
                            DB::table('menu')->where('id_menu','=',$menu[$x]->id_menu)
                                ->update(['status_menu'=>'Tidak Tersedia']);
                        }
                        if($menu[$x]->id_bahan == $bahan[$y]->id_bahan && $bahan[$y]->sisa_bahan >= $bahan[$y]->serving_size){
                            DB::table('menu')->where('id_menu','=',$menu[$x]->id_menu)
                                ->update(['status_menu'=>'Tersedia']);
                        }
                    }
                }
            }
//            $menu = menu::all();
            $menu = DB::table('menu')
                ->join('bahan','menu.id_bahan','=','bahan.id_bahan')
                ->select('menu.*','bahan.*')
                ->get();
            return response([
                'message'=>'Retrive All Success',
                'data'=> $menu
            ],200);
        }
        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function showMenuTersedia(){
        $menu = DB::table('menu')
            ->where('status_menu','=','Tersedia')
            ->get();
        if(count($menu)>0){
            return response([
                'message'=>'Retrive All Success',
                'data'=> $menu
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function show($id){
        $menu = menu::find($id);

        if(!is_null($menu)){
            return response([
                'message'=>'Retrive Menu Success',
                'data'=> $menu
            ],200);
        }

        return response([
            'message'=>'Menu Not Found',
            'data'=>null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_bahan'=>'required|numeric|exists:bahan,id_bahan',
            'nama_menu'=>'required|exists:bahan,nama_bahan',
            'deskripsi'=>'required',
            'unit'=>'required',
            'harga'=>'required|numeric',
            'status_menu'=>'required',
            'kategori'=>'required',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()],460);

        $menu = menu::create($storeData);
        return response([
            'message' => 'Add Menu Success',
            'data' => $menu,
        ],200);
    }

    public function destroy($id){
        $menu=menu::find($id);

        if(is_null($menu)){
            return response([
                'message'=>'Menu Not Found',
                'data'=>null,
            ],400);
        }

        if($menu->delete()){
            return response([
                'message'=>'Delete Menu Success',
                'data'=>$menu,
            ],200);
        }

        return response([
            'message'=>'Delete Menu Failed',
            'data'=>null,
        ],400);
    }

    public function update(Request $request,$id){
        $menu = menu::find($id);
        if(is_null($menu)){
            return response([
                'message'=>'Menu not Found',
                'data'=>null,
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'id_bahan'=>'required|numeric|exists:bahan,id_bahan',
            'nama_menu'=>'required|exists:bahan,nama_bahan',
            'deskripsi'=>'required',
            'unit'=>'required',
            'harga'=>'required|numeric',
            'status_menu'=>'required',
            'kategori'=>'required',
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $menu->id_bahan = $updateData['id_bahan'];
        $menu->nama_menu = $updateData['nama_menu'];
        $menu->deskripsi = $updateData['deskripsi'];
        $menu->unit = $updateData['unit'];
        $menu->harga = $updateData['harga'];
        $menu->status_menu = $updateData['status_menu'];
        $menu->kategori = $updateData['kategori'];

        if ($menu->save()) {
            return response([
                'message' => 'Update Menu Success',
                'data' => $menu,
            ], 200);
        }
        return response([
            'message' => 'Update Menu Failed',
            'data' => null,
        ], 400);
    }

    public function top3(){
        $result = DB::select("SELECT * FROM menu ORDER BY id_menu LIMIT 3;");
        if(count($result)>0){
            return response([
                'message'=>'Retrive All Success',
                'data'=> $result
            ],200);
        }

    }
}
