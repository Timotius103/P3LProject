<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class transaksi extends Model
{
    public $table = 'transaksi';

    protected $primaryKey = 'id_transaksi';
    protected $fillable =[
        'id_reservasi',
        'id_karyawan',
        'id_kartu',
        'total_harga',
        'jenis_pembayaran',
        'total_bayar',
        'kembalian',
        'tanggal_pembayaran',
        'jam_pembayaran',
        'nomor_struk',
        'kode_verifikasi',
    ];

    public function getCreatedAtAttribute(){
        if(!is_null($this->attributes['created_at'])) {
            return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
        }
    } //convert attribute created_at ke format Y-m-d H:i:s

    public function getUpdatedAtAttribute(){
        if(!is_null($this->attributes['updated_at'])) {
            return Carbon::parse($this->attributes['updated_at'])->format('Y-m-d H:i:s');
        }
    } //convert attribute updated_at ke format Y-m-d H:i:s
}
