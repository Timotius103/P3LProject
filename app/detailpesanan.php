<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class detailpesanan extends Model
{
    public $table = 'detailpesanan';

    protected $primaryKey = 'id_detailpesanan';
    protected $fillable =[
        'id_pesanan','id_menu','urutan_pesanan','jmlh_pesanan',
        'status_detailpesanan'
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
