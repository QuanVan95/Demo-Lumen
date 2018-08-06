<?php

namespace App\Repositories;

use App\Models\Plan;

class PlanRepository
{
    /**
     * Function get all plans
     */
    public function getAllPlans($page = 1, $limit = 10) {
        try
        {
            $planList = Plan::select('*')->offset(($page - 1) * $limit)
                ->limit($limit)->get();
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
            if (empty($oldPlan)) {
                return false;
            }
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

    public function getPagination($page = 1, $limit = 10){
        $totalResult = Plan::count();
        return [
            "page" => $page,
            "limit" => $limit,
            "total" => $totalResult
        ];
    }

}

