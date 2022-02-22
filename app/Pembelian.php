<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\MultiTenant;

class Pembelian extends Model
{
    use MultiTenant;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pembelian';

    public function pembelian_items()
    {
        return $this->hasMany('App\PembelianItem',"pembelian_id");
    }

    public function supplier()
    {
        return $this->belongsTo('App\Supplier',"supplier_id")->withDefault();
    }

    public function tax()
    {
        return $this->belongsTo('App\Tax',"tax_id")->withDefault();
    }

    public function getOrderDateAttribute($value)
    {
		$date_format = get_date_format();
        return \Carbon\Carbon::parse($value)->format("$date_format");
    }

}