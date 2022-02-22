<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payment_histories';
	
	public function user()
    {
        return $this->belongsTo('App\User')->withDefault();
    }

    public function getCreatedAtAttribute($value)
    {
		$date_format = get_date_format();
		$time_format = get_time_format();
        return \Carbon\Carbon::parse($value)->format("$date_format $time_format");
    }
}