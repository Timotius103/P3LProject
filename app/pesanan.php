<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class pesanan extends Model
{
    public $table = 'pesanan';

    protected $primaryKey = 'id_pesanan';
    protected $fillable =[
        'id_reservasi','tanggal_pesanan',
        'waktu_pesanan'
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
