<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\MultiTenant;

class Cabang extends Model
{
    use MultiTenant;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cabang';
    
    public function gudang()
    {
        return $this->hasOne('App\Gudang',"cabang_id","id")->withDefault();
    }
    
}