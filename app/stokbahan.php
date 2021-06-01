<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class stokbahan extends Model
{
    public $table = 'stokbahan';

    protected $primaryKey = 'id_stokBahan';
    protected $fillable =[
        'id_bahan','jmlh_beli','tgl_kadaluarsa','harga_beli','tgl_beli','jmlh_buang'
        ,'status_stok'
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
