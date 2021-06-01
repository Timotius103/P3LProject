<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\transaksi;
use Validator;

class TransaksiController extends Controller
{
    public function index(){
//        $transaksi = transaksi::all();
        $transaksi = DB::table('transaksi')
            ->join('reservasi','transaksi.id_reservasi','=','reservasi.id_reservasi')
            ->join('karyawan','reservasi.id_karyawan','=','karyawan.id_karyawan')
            ->select('transaksi.*','reservasi.*','karyawan.*')
            ->get();
        if(count($transaksi)>0){
            return response([
                'message'=>'Retrive All Success',
                'data'=> $transaksi
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }



    public function showtransaksibyreservasi(Request $request){
        $request = $request->all();
        $transaksi = DB::table('transaksi')
            ->join('reservasi','transaksi.id_reservasi','=','reservasi.id_reservasi')
            ->select('transaksi.*','reservasi.*')
            ->where('transaksi.id_reservasi','=',$request['id_reservasi'])
            ->get();
        if(count($transaksi)>0){
            return response([
                'message'=>'Retrive All Success',
                'data'=> $transaksi
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function gettransaksibyid(Request $request){
        $request = $request->all();
        $transaksi = DB::table('transaksi')
            ->join('reservasi','transaksi.id_reservasi','=','reservasi.id_reservasi')
            ->join('karyawan','transaksi.id_karyawan','=','karyawan.id_karyawan')
            ->join('customer','reservasi.id_customer','=','customer.id_customer')
            ->join('meja','reservasi.id_meja','=','meja.id_meja')
            ->select('transaksi.*','reservasi.*','karyawan.*','customer.*','meja.*')
            ->where('transaksi.id_transaksi','=',$request['id_transaksi'])
            ->get();
        if(count($transaksi)>0){
            return response([
                'message'=>'Retrive All Success',
                'data'=> $transaksi
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function show($id){
        $transaksi = transaksi::find($id);

        if(!is_null($transaksi)){
            return response([
                'message'=>'Retrive transaksi Success',
                'data'=> $transaksi
            ],200);
        }

        return response([
            'message'=>'transaksi Not Found',
            'data'=>null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_reservasi'=>'required|numeric',
            'id_karyawan'=>'required|numeric',
            'id_kartu'=>'',
            'total_harga'=>'numeric',
            'jenis_pembayaran'=>'',
            'total_bayar'=>'',
            'kembalian'=>'',
            'tanggal_pembayaran'=>'required|date',
            'jam_pembayaran'=>'required|regex:/(\d+\:\d+\:\d+)/',
            'nomor_struk'=>'',
            'kode_verifikasi'=>'',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()],460);
//        $date = $storeData['tanggal_pembayaran'];
//        $exploiddate = explode(" ",$date);
//        $day = $exploiddate[2];
//        $month = $exploiddate[1];
//        $year = $exploiddate[0];
        $date = new DateTime($storeData['tanggal_pembayaran']);
        $baru = $date->format('dmy');
        $storeData['nomor_struk'] =  "AKB-".$baru."-".strval(intval(transaksi::where('tanggal_pembayaran','=',$storeData['tanggal_pembayaran'])->count())+1);
        $transaksi = transaksi::create($storeData);
        return response([
            'message' => 'Add transaksi Success',
            'data' => $transaksi,
        ],200);
    }

    public function destroy($id){
        $transaksi=transaksi::find($id);

        if(is_null($transaksi)){
            return response([
                'message'=>'transaksi Not Found',
                'data'=>null,
            ],400);
        }

        if($transaksi->delete()){
            return response([
                'message'=>'Delete transaksi Success',
                'data'=>$transaksi,
            ],200);
        }

        return response([
            'message'=>'Delete transaksi Failed',
            'data'=>null,
        ],400);
    }

    public function update(Request $request,$id){
        $transaksi = transaksi::find($id);
        if(is_null($transaksi)){
            return response([
                'message'=>'transaksi not Found',
                'data'=>null,
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'id_reservasi'=>'required|numeric',
            'id_karyawan'=>'required|numeric',
            'id_kartu'=>'',
            'total_harga'=>'numeric',
            'jenis_pembayaran'=>'',
            'total_bayar'=>'',
            'kembalian'=>'',
            'tanggal_pembayaran'=>'required|date',
            'jam_pembayaran'=>'required|regex:/(\d+\:\d+\:\d+)/',
            'nomor_struk'=>'',
            'kode_verifikasi'=>'',
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $date = new DateTime($updateData['tanggal_pembayaran']);
        $baru = $date->format('dmy');
        $storeData['nomor_struk'] =  "AKB-".$baru."-".strval(intval(transaksi::where('tanggal_pembayaran','=',$updateData['tanggal_pembayaran'])->count())+1);

        $transaksi->id_reservasi = $updateData['id_reservasi'];
        $transaksi->id_karyawan = $updateData['id_karyawan'];
        $transaksi->total_harga = $updateData['total_harga'];
        $transaksi->jenis_pembayaran = $updateData['jenis_pembayaran'];
        $transaksi->total_bayar = $updateData['total_bayar'];
        $transaksi->kembalian = $updateData['kembalian'];
        $transaksi->tanggal_pembayaran = $updateData['tanggal_pembayaran'];
        $transaksi->jam_pembayaran = $updateData['jam_pembayaran'];
        $transaksi->nomor_struk = $storeData['nomor_struk'];
        $transaksi->kode_verifikasi = $updateData['kode_verifikasi'];


        if ($transaksi->save()) {
            return response([
                'message' => 'Update transaksi Success',
                'data' => $transaksi,
            ], 200);
        }
        return response([
            'message' => 'Update transaksi Failed',
            'data' => null,
        ], 400);
    }

    public function updatedipembayaran(Request $request,$id){
        $transaksi = transaksi::find($id);
        if(is_null($transaksi)){
            return response([
                'message'=>'transaksi not Found',
                'data'=>null,
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'total_harga'=>'numeric',
            'total_bayar'=>'',
            'kembalian'=>'',
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $transaksi->total_harga = $updateData['total_harga'];
        $transaksi->total_bayar = $updateData['total_bayar'];
        $transaksi->kembalian = $updateData['kembalian'];

        if ($transaksi->save()) {
            return response([
                'message' => 'Update transaksi Success',
                'data' => $transaksi,
            ], 200);
        }
        return response([
            'message' => 'Update transaksi Failed',
            'data' => null,
        ], 400);
    }
}
