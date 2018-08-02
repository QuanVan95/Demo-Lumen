<?php

namespace App\Http\Controllers;

use App\Http\Models\Plan;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;


class PlanController extends BaseController
{
    protected $planModel;
    protected $data;

    public function __construct(Request $request, Plan $plan)
    {
        $this->planModel = $plan;          // Model plan
        $this->data      = $request->all(); // Get all request
    }

    /**
     * Controller get all plans
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function getAllPlans()
    {
        $plans = $this->planModel->getAllPlans();
        if( empty($plans) ) {
            return $this->getResponse(true, []);
        }
        return $this->getResponse(true, $plans);
    }

    /**
     * Controller get plan by id
     * @param $id
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function getPlanById($id)
    {
        if (empty($id)) {
            return $this->getResponse(false, [
                'code'    => 400,
                'message' => 'Cannot get data'
            ]);
        }
        $plan = $this->planModel->getPlanById($id);
        if (!$plan) {
            return $this->getResponse(false, [
                'code'    => 400,
                'message' => 'Element does not exist'
            ]);
        }
        return $this->getResponse(true, $plan);
    }

    /**
     * Controller create a new plan
     * @return $this|BaseController|\Illuminate\Http\JsonResponse
     */
    public function createPlan()
    {
        $validation = Validator::make($this->data,
            ['plan_name'          => 'required'],
            ['plan_name.required' => 'Plan name is required']
        );
        if ($validation->fails()) {
            return $this->checkValidate($validation);
        }

        $plan = $this->planModel->createPlan($this->data);
        if (!$plan) {
            return $this->getResponse(false, [
                'code'    => 400,
                'message' => 'Cannot create data'
            ]);
        }
        return $this->getResponse(true, $plan);
    }

    /**
     * Controller update info of a plan
     * @param $id
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function updatePlan($id)
    {
        if (empty($id) || empty($this->data)) {
            return $this->getResponse(false, [
                'code'    => 400,
                'message' => 'Cannot update data'
            ]);
        };
        $item = $this->planModel->updatePlan($this->data, $id);
        if (!$item) return $this->getResponse(false, [
            'code'    => 400,
            'message' => 'Cannot update data!'
        ]);
        return $this->getResponse(true, $item);

    }

    /**
     * Controller delete a plan (update status 1 -> 0)
     * @param $id
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function deletePlan($id)
    {
        if (empty($id)) {
            return $this->getResponse(false, [
                'code'    => 400,
                'message' => 'Cannot delete data'
            ]);
        }
        $item = $this->planModel->deletePlan($id);
        if(!$item){
            return $this->getResponse(false, ['code' => '', 'message' => 'Cannot delete data!']);
        }
        return $this->getResponse(true, ['code' => '', 'message' => 'Delete successfully']);
    }
}