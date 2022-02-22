<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\MultiTenant;

class Contact extends Model
{
    use MultiTenant;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contacts';
	
	public function group()
    {
        return $this->belongsTo('App\ContactGroup', 'group_id')->withDefault(['name' => '']);
    }
	
	public function user()
    {
        return $this->belongsTo('App\User')->withDefault();
    }
}