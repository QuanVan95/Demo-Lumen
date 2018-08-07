<?php

namespace App\Repositories;

use App\Models\Plan;

class PlanRepository
{
    /**
     * Function get all plans
     */
    public function getAllPlans($page = 1, $limit = 10)
    {
        try {
            $planList = Plan::select('*')->offset(($page - 1) * $limit)
                ->limit($limit)->get();
            if (!empty($planList)) {
                foreach ($planList as $key => $plan) {
                    $planList[$key] = $this->decodePlanData($plan);  //Decode plan data before return
                }
            }
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
    public function getPlanById($id, $fields = ["*"])
    {
        try {
            $plan = Plan::select($fields)->where('plan_id', $id)->get()->first();
            if (empty($plan)) {
                return false;
            }
            $plan = $this->decodePlanData($plan);
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
        try {
            $data['status'] = 1;
            $data = $this->encodePlanData($data);  //Encode plan data before saving
            $plan           = Plan::create($data);
            $plan = $this->decodePlanData($plan); //Decode plan data and then return
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
            $oldPlan = Plan::select('*')->where('plan_id', $id)->get()->first();
            if (empty($oldPlan)) {
                return false;
            }
            $data = $this->encodePlanData($data); //Encode plan data before saving
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
            $plan = Plan::select('plan_id', 'status')->where([['plan_id', '=', $id], ['status', '=', 1]])->get()->first();
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

    /**
     * Function get pagination info when get all data (page, limit, total records)
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getPagination($page = 1, $limit = 10)
    {
        $totalResult = Plan::count();
        return [
            "page"  => $page,
            "limit" => $limit,
            "total" => $totalResult
        ];
    }

    /**
     * Function decode plan data before return
     * @param $plan
     * @return mixed
     */
    public function decodePlanData($plan)
    {
        try {
            $plan->packages               = $plan->packages ? json_decode($plan->packages) : '';
            $plan->allow_payment_gateways = $plan->packages ? json_decode($plan->allow_payment_gateways) : '';
            $plan->display_platforms      = $plan->packages ? json_decode($plan->display_platforms) : '';
            $plan->free_plans             = $plan->packages ? json_decode($plan->free_plans) : '';
            return $plan;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Function encode plan data before saving in to DTB
     * @param $data
     * @return mixed
     */
    public function encodePlanData($data)
    {
        try {
            $data['packages']               = isset($data['packages']) ? json_encode($data['packages']) : '';
            $data['allow_payment_gateways'] = isset($data['allow_payment_gateways']) ? json_encode($data['allow_payment_gateways']) : '';
            $data['display_platforms']      = isset($data['display_platforms']) ? json_encode($data['display_platforms']) : '';
            $data['free_plans']             = isset($data['free_plans']) ? json_encode($data['free_plans']) : '';
            return $data;
        } catch (\Exception $e) {
            return false;
        }
    }

}

