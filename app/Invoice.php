<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\MultiTenant;

class Invoice extends Model
{
    use MultiTenant;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoices';
    
	public function invoice_items()
    {
        return $this->hasMany('App\InvoiceItem',"invoice_id");
    }

    public function taxes()
    {
        return $this->hasMany('App\InvoiceItemTax',"invoice_id");
    }

    public function client()
    {
        return $this->belongsTo('App\Contact',"client_id")->withDefault();
    }

    public function getDueDateAttribute($value)
    {
		$date_format = get_date_format();
        return \Carbon\Carbon::parse($value)->format("$date_format");
    }

    public function getInvoiceDateAttribute($value)
    {
		$date_format = get_date_format();
        return \Carbon\Carbon::parse($value)->format("$date_format");
    }

}