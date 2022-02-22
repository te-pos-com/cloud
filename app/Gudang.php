<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\MultiTenant;

class Gudang extends Model
{
    use MultiTenant;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'gudang' ;
    
    public function cabang()
    {
        return $this->hasOne('App\Cabang',"id","cabang_id")->withDefault();
    }
    
}