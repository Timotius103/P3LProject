<?php

namespace App\Http\Controllers\Api;

use App\bahan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\history;
use Validator;
use Illuminate\Support\Facades\DB;
use function MongoDB\BSON\fromJSON;

class HistoryController extends Controller
{
    public function index(){
//        $history = history::all();
        $history = DB::table('history')
            ->join('bahan','history.id_bahan','=','bahan.id_bahan')
            ->select('history.*','bahan.*')
            ->get();
        if(count($history)>0){
            return response([
                'message'=>'Retrive All Success',
                'data'=> $history
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function readByPesanan($idBahan){
//        $history = history::all();
        $history = history::where([
            ['id_bahan','=',$idBahan],
            ['jmlh_buang','=',],
            ['tgl_buang','=',],
            ['status_history','=',],
        ])->get();
        if(count($history)>0){
            return response([
                'message'=>'Retrive All Success',
                'data'=> $history
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],404);
    }

    public function show($id){
        $history = history::find($id);

        if(!is_null($history)){
            return response([
                'message'=>'Retrive history Success',
                'data'=> $history
            ],200);
        }

        return response([
            'message'=>'history Not Found',
            'data'=>null
        ],404);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_bahan'=>'required|numeric',
            'jmlh_buang'=>'required | numeric',
            'tgl_buang'=>'required|date',
            'status_history'=>'required',
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()],460);

        $bahan = bahan::where('id_bahan', '=', $storeData['id_bahan'])->select('bahan.*')->first();
        if(!is_null($bahan)){
            $storeData['sisa_bahansaatini'] = $bahan->sisa_bahan;
        }
        $history = history::create($storeData);
        if($bahan->save()){
            return response([
                'message' => 'Add history Success',
                'data' => $history,
            ],200);
        }
    }

    public function destroy($id){
        $history=history::find($id);

        if(is_null($history)){
            return response([
                'message'=>'history Not Found',
                'data'=>null,
            ],400);
        }

        if($history->delete()){
            return response([
                'message'=>'Delete history Success',
                'data'=>$history,
            ],200);
        }

        return response([
            'message'=>'Delete history Failed',
            'data'=>null,
        ],400);
    }

    public function update(Request $request,$id){
        $history = history::find($id);
        if(is_null($history)){
            return response([
                'message'=>'history not Found',
                'data'=>null,
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'id_bahan'=>'required|numeric',
            'jmlh_buang'=>'required|numeric',
            'tgl_buang'=>'required|date',
            'status_history'=>'required',
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $history->id_bahan = $updateData['id_bahan'];
        $history->jmlh_buang = $updateData['jmlh_buang'];
        $history->tgl_buang = $updateData['tgl_buang'];
        $history->status_history = $updateData['status_history'];

        if ($history->save()) {
            return response([
                'message' => 'Update history Success',
                'data' => $history,
            ], 200);
        }
        return response([
            'message' => 'Update history Failed',
            'data' => null,
        ], 400);
    }

    public function laporanPengeluaranBulanan($laporanYear) {
        $newTahun = $laporanYear;

        $bulan = array(
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
        );

        for($i=1; $i<=12; $i++) {
            $makananUtama[$i] = DB::table('history')
                ->join('bahan', 'history.id_bahan', '=', 'bahan.id_bahan')
                ->join('menu', 'bahan.id_bahan', '=', 'menu.id_bahan')
                ->selectRaw('ifnull(sum(history.jmlh_beli * history.hargabeli_stok), 0) as SubTotalMakananUtama')
                ->where('menu.kategori', '=', 'Menu Makanan Utama')
//                ->where('history.status_stokmasuk', '=', 0)
                ->whereMonth('history.tgl_buang', '=', $i)
                ->whereYear('history.tgl_buang', '=', $laporanYear)
                ->first();

            $makananSideDish[$i] = DB::table('history')
                ->join('bahan', 'history.id_bahan', '=', 'bahan.id_bahan')
                ->join('menu', 'bahan.id_bahan', '=', 'menu.id_bahan')
                ->selectRaw('ifnull(sum(history.jmlh_beli * history.hargabeli_stok), 0) as SubTotalMakananSideDish')
                ->where('menu.kategori', '=', 'Menu Makanan Side Dish')
//                ->where('history.status_stokmasuk', '=', 0)
                ->whereMonth('history.tgl_buang', '=', $i)
                ->whereYear('history.tgl_buang', '=', $laporanYear)
                ->first();
//                ->join('bahan_bakus', 'stok_bahan_masuks.id_bahan', '=', 'bahan_bakus.id')
//                ->join('menus', 'bahan_bakus.id', '=', 'menus.id_bahan')
//                ->selectRaw('ifnull(sum(stok_bahan_masuks.harga), 0) as SubTotalMakananSideDish')
//                ->where('menus.tipe_menu', '=', 'Makanan Side Dish')
//                ->where('stok_bahan_masuks.status_stokmasuk', '=', 0)
//                ->whereMonth('stok_bahan_masuks.tanggal_masuk', '=', $i)
//                ->whereYear('stok_bahan_masuks.tanggal_masuk', '=', $laporanYear)
//                ->first();

            $minuman[$i] = DB::table('history')
                ->join('bahan', 'history.id_bahan', '=', 'bahan.id_bahan')
                ->join('menu', 'bahan.id_bahan', '=', 'menu.id_bahan')
                ->selectRaw('ifnull(sum(history.jmlh_beli * history.hargabeli_stok), 0) as SubTotalMinuman')
                ->where('menu.kategori', '=', 'Menu Minuman')
//                ->where('history.status_stokmasuk', '=', 0)
                ->whereMonth('history.tgl_buang', '=', $i)
                ->whereYear('history.tgl_buang', '=', $laporanYear)
                ->first();

            $nomor[$i] = $i;

            $laporanPengeluaranbulanan[$i] = $makananUtama[$i]->SubTotalMakananUtama +
                $makananSideDish[$i]->SubTotalMakananSideDish +
                $minuman[$i]->SubTotalMinuman;

            $outputPengeluaranBulanan[$i] = array(
                "nomor" => $nomor[$i],
                "bulan" => $bulan[$i-1],
                "makanan_utama" => $makananUtama[$i]->SubTotalMakananUtama,
                "makanan_side_dish" => $makananSideDish[$i]->SubTotalMakananSideDish,
                "minuman" => $minuman[$i]->SubTotalMinuman,
                "total_pengeluaran_bulanan" => $laporanPengeluaranbulanan[$i]
            );
        }
        return response([
            'tahun' => $newTahun,
            'message' => 'Tampil Pengeluaran Bulanan',
            'data' => $outputPengeluaranBulanan,
        ], 200);

        return response([
            'message' => 'Kosong',
            'data' => null,
        ], 400);
    }

    public function laporanPengeluaranTahunan($tahunAwal, $tahunAkhir) {
        $awal = $tahunAwal;
        $akhir = $tahunAkhir;
        $temp = 0;

        $intAwal = (int)$awal;
        $intAkhir = (int)$akhir;

        $range = $intAkhir - $intAwal;


        for($i=1; $i<=$range+1; $i++) {
            if($temp > 0) {
                $makananUtama[$i] = DB::table('history')
                    ->join('bahan', 'history.id_bahan', '=', 'bahan.id_bahan')
                    ->join('menu', 'bahan.id_bahan', '=', 'menu.id_bahan')
                    ->selectRaw('ifnull(sum(history.jmlh_beli * history.hargabeli_stok), 0) as SubTotalMakananUtama')
                    ->where('menu.kategori', '=', 'Menu Makanan Utama')
                    ->whereYear('history.tgl_buang', '=', $intAwal+$temp)
                    ->first();
//                    ->join('bahan_bakus', 'stok_bahan_masuks.id_bahan', '=', 'bahan_bakus.id')
//                    ->join('menus', 'bahan_bakus.id', '=', 'menus.id_bahan')
//                    ->selectRaw('ifnull(sum(stok_bahan_masuks.harga), 0) as SubTotalMakananUtama')
//                    ->where('menus.tipe_menu', '=', 'Makanan Utama')
//                    ->where('stok_bahan_masuks.status_stokmasuk', '=', 0)
//                    ->whereYear('stok_bahan_masuks.tanggal_masuk', '=', $intAwal+$temp)
//                    ->first();

                $makananSideDish[$i] = DB::table('history')
                    ->join('bahan', 'history.id_bahan', '=', 'bahan.id_bahan')
                    ->join('menu', 'bahan.id_bahan', '=', 'menu.id_bahan')
                    ->selectRaw('ifnull(sum(history.jmlh_beli * history.hargabeli_stok), 0) as SubTotalMakananSideDish')
                    ->where('menu.kategori', '=', 'Menu Makanan Side Dish')
                    ->whereYear('history.tgl_buang', '=', $intAwal+$temp)
                    ->first();

                $minuman[$i] = DB::table('history')
                    ->join('bahan', 'history.id_bahan', '=', 'bahan.id_bahan')
                    ->join('menu', 'bahan.id_bahan', '=', 'menu.id_bahan')
                    ->selectRaw('ifnull(sum(history.jmlh_beli * history.hargabeli_stok), 0) as SubTotalMinuman')
                    ->where('menu.kategori', '=', 'Menu Minuman')
                    ->whereYear('history.tgl_buang', '=', $intAwal+$temp)
                    ->first();

                $tahun[$i] = $intAwal + $temp;
                $temp++;
            }else{
                $makananUtama[$i] = DB::table('history')
                    ->join('bahan', 'history.id_bahan', '=', 'bahan.id_bahan')
                    ->join('menu', 'bahan.id_bahan', '=', 'menu.id_bahan')
                    ->selectRaw('ifnull(sum(history.jmlh_beli * history.hargabeli_stok), 0) as SubTotalMakananUtama')
                    ->where('menu.kategori', '=', 'Menu Makanan Utama')
                    ->whereYear('history.tgl_buang', '=', $intAwal)
                    ->first();

                $makananSideDish[$i] = DB::table('history')
                    ->join('bahan', 'history.id_bahan', '=', 'bahan.id_bahan')
                    ->join('menu', 'bahan.id_bahan', '=', 'menu.id_bahan')
                    ->selectRaw('ifnull(sum(history.jmlh_beli * history.hargabeli_stok), 0) as SubTotalMakananSideDish')
                    ->where('menu.kategori', '=', 'Menu Makanan Side Dish')
                    ->whereYear('history.tgl_buang', '=', $intAwal)
                    ->first();

                $minuman[$i] = DB::table('history')
                    ->join('bahan', 'history.id_bahan', '=', 'bahan.id_bahan')
                    ->join('menu', 'bahan.id_bahan', '=', 'menu.id_bahan')
                    ->selectRaw('ifnull(sum(history.jmlh_beli * history.hargabeli_stok), 0) as SubTotalMinuman')
                    ->where('menu.kategori', '=', 'Menu Minuman')
                    ->whereYear('history.tgl_buang', '=', $intAwal)
                    ->first();

                $tahun[$i] = $intAwal;
                $temp = 1;
            }

            $laporanPengeluarantahunan[$i] = $makananUtama[$i]->SubTotalMakananUtama +
                $makananSideDish[$i]->SubTotalMakananSideDish +
                $minuman[$i]->SubTotalMinuman;
            $nomor[$i] = $i;

            $outputPengeluaranTahunan[$i] = array(
                "nomor" => $nomor[$i],
                "tahun" => $tahun[$i],
                "makanan_utama" => $makananUtama[$i]->SubTotalMakananUtama,
                "makanan_side_dish" => $makananSideDish[$i]->SubTotalMakananSideDish,
                "minuman" => $minuman[$i]->SubTotalMinuman,
                "total_pengeluaran_bulanan" => $laporanPengeluarantahunan[$i]
            );
        }

        return response([
            'message' => 'Tampil Pengeluaran Bulanan',
            'data' => $outputPengeluaranTahunan,
        ], 200);

        return response([
            'message' => 'Kosong',
            'data' => null,
        ], 400);
    }


    public function laporanPendapatanBulanan($laporanYear) {
        $newTahun = $laporanYear;

        $bulan = array(
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
        );

        for($i=1; $i<=12; $i++) {
            $makananUtama[$i] = DB::table('detailpesanan')
                ->join('menu', 'detailpesanan.id_menu', '=', 'menu.id_menu')
                ->join('pesanan','detailpesanan.id_pesanan','=','pesanan.id_pesanan')
//                ->join('menu', 'bahan.id_bahan', '=', 'menu.id_bahan')
                ->selectRaw('ifnull(sum(detailpesanan.jmlh_pesanan * menu.harga), 0) as SubTotalMakananUtama')
                ->where('menu.kategori', '=', 'Menu Makanan Utama')
//                ->where('history.status_stokmasuk', '=', 0)
                ->whereMonth('pesanan.tanggal_pesanan', '=', $i)
                ->whereYear('pesanan.tanggal_pesanan', '=', $laporanYear)
                ->first();

            $makananSideDish[$i] = DB::table('detailpesanan')
                ->join('menu', 'detailpesanan.id_menu', '=', 'menu.id_menu')
                ->join('pesanan','detailpesanan.id_pesanan','=','pesanan.id_pesanan')
                //                ->join('menu', 'bahan.id_bahan', '=', 'menu.id_bahan')
                ->selectRaw('ifnull(sum(detailpesanan.jmlh_pesanan * menu.harga), 0) as SubTotalMakananSideDish')
                ->where('menu.kategori', '=', 'Menu Makanan Side Dish')
//                ->where('history.status_stokmasuk', '=', 0)
                ->whereMonth('pesanan.tanggal_pesanan', '=', $i)
                ->whereYear('pesanan.tanggal_pesanan', '=', $laporanYear)
                ->first();

            $minuman[$i] = DB::table('detailpesanan')
                ->join('menu', 'detailpesanan.id_menu', '=', 'menu.id_menu')
                ->join('pesanan','detailpesanan.id_pesanan','=','pesanan.id_pesanan')
                //                ->join('menu', 'bahan.id_bahan', '=', 'menu.id_bahan')
                ->selectRaw('ifnull(sum(detailpesanan.jmlh_pesanan * menu.harga), 0) as SubTotalMinuman')
                ->where('menu.kategori', '=', 'Menu Minuman')
//                ->where('history.status_stokmasuk', '=', 0)
                ->whereMonth('pesanan.tanggal_pesanan', '=', $i)
                ->whereYear('pesanan.tanggal_pesanan', '=', $laporanYear)
                ->first();

            $nomor[$i] = $i;

            $laporanPendapatanbulanan[$i] = $makananUtama[$i]->SubTotalMakananUtama +
                $makananSideDish[$i]->SubTotalMakananSideDish +
                $minuman[$i]->SubTotalMinuman;

            $outputPendapatanbulanan[$i] = array(
                "nomor" => $nomor[$i],
                "bulan" => $bulan[$i-1],
                "makanan_utama" => $makananUtama[$i]->SubTotalMakananUtama,
                "makanan_side_dish" => $makananSideDish[$i]->SubTotalMakananSideDish,
                "minuman" => $minuman[$i]->SubTotalMinuman,
                "total_pengeluaran_bulanan" => $laporanPendapatanbulanan[$i]
            );
        }
        return response([
            'tahun' => $newTahun,
            'message' => 'Tampil Pendapatan Bulanan',
            'data' => $outputPendapatanbulanan,
        ], 200);

        return response([
            'message' => 'Kosong',
            'data' => null,
        ], 400);
    }

    public function laporanPendapatanTahunan($tahunAwal, $tahunAkhir) {
        $awal = $tahunAwal;
        $akhir = $tahunAkhir;
        $temp = 0;

        $intAwal = (int)$awal;
        $intAkhir = (int)$akhir;

        $range = $intAkhir - $intAwal;


        for($i=1; $i<=$range+1; $i++) {
            if($temp > 0) {
                $makananUtama[$i] = DB::table('detailpesanan')
                    ->join('menu', 'detailpesanan.id_menu', '=', 'menu.id_menu')
                    ->join('pesanan','detailpesanan.id_pesanan','=','pesanan.id_pesanan')
                    ->selectRaw('ifnull(sum(detailpesanan.jmlh_pesanan * menu.harga), 0) as SubTotalMakananUtama')
                    ->where('menu.kategori', '=', 'Menu Makanan Utama')
                    ->whereYear('pesanan.tanggal_pesanan', '=', $intAwal+$temp)
                    ->first();

                $makananSideDish[$i] = DB::table('detailpesanan')
                    ->join('menu', 'detailpesanan.id_menu', '=', 'menu.id_menu')
                    ->join('pesanan','detailpesanan.id_pesanan','=','pesanan.id_pesanan')
                    ->selectRaw('ifnull(sum(detailpesanan.jmlh_pesanan * menu.harga), 0) as SubTotalMakananSideDish')
                    ->where('menu.kategori', '=', 'Menu Makanan Side Dish')
                    ->whereYear('pesanan.tanggal_pesanan', '=', $intAwal+$temp)
                    ->first();

                $minuman[$i] = DB::table('detailpesanan')
                    ->join('menu', 'detailpesanan.id_menu', '=', 'menu.id_menu')
                    ->join('pesanan','detailpesanan.id_pesanan','=','pesanan.id_pesanan')
                    ->selectRaw('ifnull(sum(detailpesanan.jmlh_pesanan * menu.harga), 0) as SubTotalMinuman')
                    ->where('menu.kategori', '=', 'Menu Minuman')
                    ->whereYear('pesanan.tanggal_pesanan', '=', $intAwal+$temp)
                    ->first();

                $tahun[$i] = $intAwal + $temp;
                $temp++;
            }else{
                $makananUtama[$i] = DB::table('detailpesanan')
                    ->join('menu', 'detailpesanan.id_menu', '=', 'menu.id_menu')
                    ->join('pesanan','detailpesanan.id_pesanan','=','pesanan.id_pesanan')
                    ->selectRaw('ifnull(sum(detailpesanan.jmlh_pesanan * menu.harga), 0) as SubTotalMakananUtama')
                    ->where('menu.kategori', '=', 'Menu Makanan Utama')
                    ->whereYear('pesanan.tanggal_pesanan', '=', $intAwal+$temp)
                    ->first();

                $makananSideDish[$i] = DB::table('detailpesanan')
                    ->join('menu', 'detailpesanan.id_menu', '=', 'menu.id_menu')
                    ->join('pesanan','detailpesanan.id_pesanan','=','pesanan.id_pesanan')
                    ->selectRaw('ifnull(sum(detailpesanan.jmlh_pesanan * menu.harga), 0) as SubTotalMakananSideDish')
                    ->where('menu.kategori', '=', 'Menu Makanan Side Dish')
                    ->whereYear('pesanan.tanggal_pesanan', '=', $intAwal+$temp)
                    ->first();

                $minuman[$i] = DB::table('detailpesanan')
                    ->join('menu', 'detailpesanan.id_menu', '=', 'menu.id_menu')
                    ->join('pesanan','detailpesanan.id_pesanan','=','pesanan.id_pesanan')
                    ->selectRaw('ifnull(sum(detailpesanan.jmlh_pesanan * menu.harga), 0) as SubTotalMinuman')
                    ->where('menu.kategori', '=', 'Menu Minuman')
                    ->whereYear('pesanan.tanggal_pesanan', '=', $intAwal+$temp)
                    ->first();

                $tahun[$i] = $intAwal;
                $temp = 1;
            }

            $laporanPendapatantahunan[$i] = $makananUtama[$i]->SubTotalMakananUtama +
                $makananSideDish[$i]->SubTotalMakananSideDish +
                $minuman[$i]->SubTotalMinuman;
            $nomor[$i] = $i;

            $outputPendapatanTahunan[$i] = array(
                "nomor" => $nomor[$i],
                "tahun" => $tahun[$i],
                "makanan_utama" => $makananUtama[$i]->SubTotalMakananUtama,
                "makanan_side_dish" => $makananSideDish[$i]->SubTotalMakananSideDish,
                "minuman" => $minuman[$i]->SubTotalMinuman,
                "total_pengeluaran_bulanan" => $laporanPendapatantahunan[$i]
            );
        }

        return response([
            'message' => 'Tampil Pendapatan Bulanan',
            'data' => $outputPendapatanTahunan,
        ], 200);

        return response([
            'message' => 'Kosong',
            'data' => null,
        ], 400);
    }


    public function laporanItemMenuTahun($laporanYear, $laporanBulan) {
        $newTahun = $laporanYear;
        $newbulan = $laporanBulan;

        $bulan = array(
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
        );

        for($i=1; $i<=12; $i++) {
            $makananUtama[$i] = DB::table('detailpesanan')
                ->join('menu', 'detailpesanan.id_menu', '=', 'menu.id_menu')
                ->join('pesanan','detailpesanan.id_pesanan','=','pesanan.id_pesanan')
//                ->join('menu', 'bahan.id_bahan', '=', 'menu.id_bahan')
                ->selectRaw('ifnull(sum(detailpesanan.jmlh_pesanan), 0) as SubTotalMakananUtama')
                ->where('menu.kategori', '=', 'Menu Makanan Utama')
//                ->where('history.status_stokmasuk', '=', 0)
                ->whereMonth('pesanan.tanggal_pesanan', '=', $laporanBulan)
                ->whereYear('pesanan.tanggal_pesanan', '=', $laporanYear)
                ->first();

            $makananSideDish[$i] = DB::table('detailpesanan')
                ->join('menu', 'detailpesanan.id_menu', '=', 'menu.id_menu')
                ->join('pesanan','detailpesanan.id_pesanan','=','pesanan.id_pesanan')
                //                ->join('menu', 'bahan.id_bahan', '=', 'menu.id_bahan')
                ->selectRaw('ifnull(sum(detailpesanan.jmlh_pesanan * menu.harga), 0) as SubTotalMakananSideDish')
                ->where('menu.kategori', '=', 'Menu Makanan Side Dish')
//                ->where('history.status_stokmasuk', '=', 0)
                ->whereMonth('pesanan.tanggal_pesanan', '=', $i)
                ->whereYear('pesanan.tanggal_pesanan', '=', $laporanYear)
                ->first();

            $minuman[$i] = DB::table('detailpesanan')
                ->join('menu', 'detailpesanan.id_menu', '=', 'menu.id_menu')
                ->join('pesanan','detailpesanan.id_pesanan','=','pesanan.id_pesanan')
                //                ->join('menu', 'bahan.id_bahan', '=', 'menu.id_bahan')
                ->selectRaw('ifnull(sum(detailpesanan.jmlh_pesanan * menu.harga), 0) as SubTotalMinuman')
                ->where('menu.kategori', '=', 'Menu Minuman')
//                ->where('history.status_stokmasuk', '=', 0)
                ->whereMonth('pesanan.tanggal_pesanan', '=', $i)
                ->whereYear('pesanan.tanggal_pesanan', '=', $laporanYear)
                ->first();

            $nomor[$i] = $i;

            $laporanPendapatanbulanan[$i] = $makananUtama[$i]->SubTotalMakananUtama +
                $makananSideDish[$i]->SubTotalMakananSideDish +
                $minuman[$i]->SubTotalMinuman;

            $outputPendapatanbulanan[$i] = array(
                "nomor" => $nomor[$i],
                "bulan" => $bulan[$i-1],
                "makanan_utama" => $makananUtama[$i]->SubTotalMakananUtama,
                "makanan_side_dish" => $makananSideDish[$i]->SubTotalMakananSideDish,
                "minuman" => $minuman[$i]->SubTotalMinuman,
                "total_pengeluaran_bulanan" => $laporanPendapatanbulanan[$i]
            );
        }
        return response([
            'tahun' => $newTahun,
            'message' => 'Tampil Pendapatan Bulanan',
            'data' => $outputPendapatanbulanan,
        ], 200);

        return response([
            'message' => 'Kosong',
            'data' => null,
        ], 400);
    }



}
