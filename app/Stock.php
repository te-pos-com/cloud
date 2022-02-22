<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\MultiTenant;

class Stock extends Model
{
    use MultiTenant;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'current_stocks';


    public function product()
    {
        return $this->belongsTo('App\Product',"product_id")->withDefault();
    }

}