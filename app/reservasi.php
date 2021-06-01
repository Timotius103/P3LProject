<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class reservasi extends Model
{
    public $table = 'reservasi';

    protected $primaryKey = 'id_reservasi';
    protected $fillable =[
        'id_karyawan','id_meja','id_customer','jam_reservasi','tanggal_reservasi'
        ,'status_reservasi','sesi_reservasi'
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
