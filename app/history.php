<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class history extends Model
{
    public $table = 'history';

    protected $primaryKey = 'id_history';
    protected $fillable =[
        'id_bahan','jmlh_buang','tgl_buang','status_history'
        ,'sisa_bahansaatini','jmlh_beli','hargabeli_stok'
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
