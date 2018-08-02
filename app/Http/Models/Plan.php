<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Plan extends Model
{
    protected $table = 'plans';
    protected $primaryKey = 'plan_id';
    const CREATED_AT = 'created_date';
    const UPDATED_AT = 'last_update';

    public $fillable = ['plan_id', 'plan_name', 'name', 'country', 'description', 'plan_description', 'plan_type', 'packages', 'drm_system_type',
        'allow_payment_gateways', 'display_platforms', 'value', 'playcoin', 'playkeng', 'discount', 'is_giftcode', 'status', 'created_date', 'last_update',
        'is_promotion', 'app_id', 'platform', 'image', 'subs_handle', 'package', 'specific', 'usd', 'specific_time', 'partner', 'free_plans'];

    /**
     * Function get all plans
     */
    public function getAllPlans() {
        try
        {
            $planList = Plan::all();
            return $planList;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Function get a plan by id
     * @param $id
     * @param array $fields
     * @return false or an object
     */
    public function getPlanById($id, $fields = ["*"]){
        try
        {
            $plan = Plan::select($fields)->where('plan_id', $id)->get()->first();
            if (empty($plan)) {
                return false;
            }
            return $plan;
        } catch (\Exception $e) {
            return false;
        }

    }

    /**
     * Function create a new plan
     * @param $data
     * @return false or an object
     */
    public function createPlan($data)
    {
        try
        {
            $data['status'] = 1 ;
            $plan = Plan::create($data);
            return $plan;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Function update date of plan
     * @param $data
     * @param $id
     * @return false or an object
     */
    public function updatePlan($data, $id)
    {
        try {
            $oldPlan = Plan::select('*')->where( 'plan_id', $id)->get()->first();
            $plan = $oldPlan->fill($data);
            $plan->save();
            return $plan;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Function delete a plan (update status 1 -> 0)
     * @param $id
     * @return false or an object
     */
    public function deletePlan($id)
    {
        try {
            $plan = Plan::select('plan_id', 'status')->where( [['plan_id', '=',  $id], ['status', '=', 1]] )->get()->first();
            if (!$plan) {
                return false;
            }
            $plan->status = 0;
            if ($plan->save()) {
                return $plan;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}

