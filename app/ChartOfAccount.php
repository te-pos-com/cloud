<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\MultiTenant;

class ChartOfAccount extends Model
{
    use MultiTenant;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chart_of_accounts';
}