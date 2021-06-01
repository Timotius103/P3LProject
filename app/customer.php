<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Laravel\Passport\HasApiTokens;
use Carbon\Carbon;

class customer extends Model
{

    public $table = 'customer';

    protected $primaryKey = 'id_customer';
    protected $fillable =[
        'nama_customer','telepon_customer','email_customer'
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
