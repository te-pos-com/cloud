<?php

namespace App;
use App\Traits\MultiTenant;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use MultiTenant;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoice_items';

    public function item()
    {
        return $this->belongsTo('App\Item',"item_id")->withDefault();
    }

    public function taxes()
    {
        return $this->hasMany('App\InvoiceItemTax',"invoice_item_id");
    }

}