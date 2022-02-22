<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\MultiTenant;

class InvoiceItemTax extends Model
{
    use MultiTenant;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoice_item_taxes';

    public function invoice_item()
    {
        return $this->belongsTo('App\InvoiceItem',"invoice_item_id")->withDefault();
    }

}