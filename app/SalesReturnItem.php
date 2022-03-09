<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\MultiTenant;

class SalesReturnItem extends Model
{
    use MultiTenant;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sales_return_items';

    public function item()
    {
        return $this->belongsTo('App\Item',"product_id")->withDefault();
    }

    public function taxes()
    {
        return $this->hasMany('App\SalesReturnItemTax',"sales_return_item_id");
    }
    
    public function gudang()
    {
        return $this->hasOne('App\Gudang',"id","gudang_id")->withDefault();
    }
}