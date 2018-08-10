<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $table = 'plans';
    protected $primaryKey = 'plan_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'last_update';

    public $fillable = ['plan_name', 'name', 'country', 'description', 'plan_description', 'plan_type', 'packages', 'drm_system_type',
        'allow_payment_gateways', 'display_platforms', 'value', 'playcoin', 'playkeng', 'discount', 'is_giftcode', 'status', 'created_date', 'last_update',
        'is_promotion', 'app_id', 'platform', 'image', 'subs_handle', 'package', 'specific', 'usd', 'specific_time', 'partner', 'free_plans'];
}

