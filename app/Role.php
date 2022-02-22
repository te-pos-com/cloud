<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\MultiTenant;

class Role extends Model
{
	use MultiTenant;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'staff_roles';
	
	protected $fillable = [
        'name',
        'description',
        'company_id',
    ];
	
	public function permissions(){
		return $this->hasMany('App\AccessControl','role_id');
	}
}