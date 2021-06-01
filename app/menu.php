<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class menu extends Model
{
    public $table = 'menu';

    protected $primaryKey = 'id_menu';
    protected $fillable =[
        'id_bahan','nama_menu','deskripsi','unit','harga','status_menu','kategori','gambar'
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
