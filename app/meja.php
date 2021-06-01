<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class meja extends Model
{
    public $table = 'meja';

    protected $primaryKey = 'id_meja';
    protected $fillable =[
        'status_meja','nomor_meja'
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
