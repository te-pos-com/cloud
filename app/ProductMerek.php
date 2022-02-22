<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\MultiTenant;

class ProductMerek extends Model
{
    use MultiTenant;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_merek';
    
    public function item()
    {
        return $this->hasOne('App\Item',"id_merek","id")->withDefault();
    }
}