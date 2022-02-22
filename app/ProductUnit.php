<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\MultiTenant;

class ProductUnit extends Model
{
    use MultiTenant;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_units';
    
    public function produk()
    {
        return $this->hasOne('App\Product',"product_unit","id")->withDefault();
    }
}