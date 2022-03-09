<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\MultiTenant;

class QuotationItem extends Model
{
    use MultiTenant;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'quotation_items';

    public function item()
    {
        return $this->belongsTo('App\Item',"item_id")->withDefault();
    }

    public function taxes()
    {
        return $this->hasMany('App\QuotationItemTax',"quotation_item_id");
    }

    public function gudang()
    {
        return $this->hasOne('App\Gudang',"id","gudang_id")->withDefault();
    }

}