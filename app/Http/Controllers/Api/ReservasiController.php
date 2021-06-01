<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\meja;
use Illuminate\Http\Request;
use App\reservasi;
use Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class ReservasiController extends Controller
{
    public function index(){
//        $reservasi = reservasi::all();
        $reservasi = DB::table('reservasi')
            ->join('customer','reservasi.id_customer','=','customer.id_customer')
            ->join('meja','reservasi.id_meja','=','meja.id_meja')
            ->join('karyawan','reservasi.id_karyawan','=','karyawan.id_karyawan')
            ->select('reservasi.*','customer.*','meja.*','karyawan.*')
            ->get();
        if(count($reservasi)>0){
            return response([
                'message'=>'Retrive All Success',
                'data'=> $reservasi
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function getreservasibyid(Request $request){
        $request = $request->all();
        $reservasi = DB::table('reservasi')
            ->where('reservasi.id_reservasi','=',$request['id_reservasi'])
            ->get();
        if(count($reservasi)>0){
            return response([
                'message'=>'Retrive All Success',
                'data'=> $reservasi
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function tampilOnGoing(){
        $reservasi = DB::table('reservasi')
            ->join('customer','reservasi.id_customer','=','customer.id_customer')
            ->join('meja','reservasi.id_meja','=','meja.id_meja')
            ->join('karyawan','reservasi.id_karyawan','=','karyawan.id_karyawan')
            ->select('reservasi.*','customer.*','meja.*','karyawan.*')
            ->where('status_reservasi','=','On Going')
            ->get();
        if(count($reservasi)>0){
            return response([
                'message'=>'Retrive All Success',
                'data'=> $reservasi
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function show($id){
        $reservasi = reservasi::find($id);

        if(!is_null($reservasi)){
            return response([
                'message'=>'Retrive reservasi Success',
                'data'=> $reservasi
            ],200);
        }

        return response([
            'message'=>'reservasi Not Found',
            'data'=>null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_karyawan'=>'required|numeric',
            'id_meja'=>'required|numeric',
            'id_customer'=>'required|numeric',
            'jam_reservasi'=>'required|regex:/(\d+\:\d+\:\d+)/',
            'status_reservasi'=>'required',
            'sesi_reservasi'=>'required',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()],460);
        $breakawal = Carbon::parse("16:00:01");
        $breakakhir = Carbon::parse("16:59:59");

        $meja = meja::where('id_meja','=',$storeData['id_meja'])->first();

        if(Carbon::parse($storeData['jam_reservasi'])->between($breakawal,$breakakhir))
            return response(['message' => 'Lagi Istirahat Bosss'],400);

        if(!is_null($meja)){
            $meja['status_meja'] = 'Terpakai';
        }
        $reservasi = reservasi::create($storeData);
        if($meja->save()){
            return response([
                'message' => 'Add reservasi Success',
                'data' => $reservasi,
            ],200);
        }
    }

    public function destroy($id){
        $reservasi=reservasi::find($id);

        if(is_null($reservasi)){
            return response([
                'message'=>'reservasi Not Found',
                'data'=>null,
            ],400);
        }

        if($reservasi->delete()){
            return response([
                'message'=>'Delete reservasi Success',
                'data'=>$reservasi,
            ],200);
        }

        return response([
            'message'=>'Delete reservasi Failed',
            'data'=>null,
        ],400);
    }

    public function update(Request $request,$id){
        $reservasi = reservasi::find($id);
        if(is_null($reservasi)){
            return response([
                'message'=>'reservasi not Found',
                'data'=>null,
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'id_karyawan'=>'required|numeric',
            'id_meja'=>'required|numeric',
            'id_customer'=>'required|numeric',
            'jam_reservasi'=>'required|regex:/(\d+\:\d+\:\d+)/',
            'tanggal_reservasi'=>'required|date',
            'status_reservasi'=>'required',
            'sesi_reservasi'=>'required',
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $breakawal = Carbon::parse("16:00:01");
        $breakakhir = Carbon::parse("16:59:59");

        $meja = meja::where('id_meja','=',$reservasi['id_meja'])->first();

        if(Carbon::parse($updateData['jam_reservasi'])->between($breakawal,$breakakhir))
            return response(['message' => 'Lagi Istirahat Bosss'],400);



        if(!is_null($meja)){
            if($updateData['status_reservasi']=='Done'){
                $meja['status_meja'] = 'Kosong';
            }
        }
        $reservasi->id_karyawan = $updateData['id_karyawan'];
        $reservasi->id_meja = $updateData['id_meja'];
        $reservasi->id_customer = $updateData['id_customer'];
        $reservasi->jam_reservasi = $updateData['jam_reservasi'];
        $reservasi->status_reservasi = $updateData['status_reservasi'];
        $reservasi->tanggal_reservasi =$updateData['tanggal_reservasi'];
        $reservasi->sesi_reservasi = $updateData['sesi_reservasi'];

        if ($reservasi->save()) {
            if($meja->save()){
                return response([
                    'message' => 'Update reservasi Success',
                    'data' => $reservasi,
                ], 200);
            }
        }
        return response([
            'message' => 'Update reservasi Failed',
            'data' => null,
        ], 400);
    }
}
