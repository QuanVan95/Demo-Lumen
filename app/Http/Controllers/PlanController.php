<?php

namespace App\Http\Controllers;

use App\Http\Base\BaseController;
use App\Models\Plan;
use App\Repositories\PlanRepository;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;


class PlanController extends BaseController
{
    protected $data;
    protected $planRepo;

    public function __construct(Request $request, Plan $plan, PlanRepository $planRepository)
    {
        $this->data     = $request->all(); // Get all request
        $this->planRepo = $planRepository;
    }

    /**
     * Controller get all plans
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function getAllPlans($page = 1, $limit = 10)
    {
        if (!empty($this->data['page'])) {
            $page = $this->data['page'];
        }
        if (!empty($this->data['limit'])) {
            $limit = $this->data['limit'];
        }

        $plans    = $this->planRepo->getAllPlans($page, $limit);
        $paginate = $this->planRepo->getPagination($page, $limit);
        if (empty($plans)) {
            return $this->getResponse(true, [], 'Get data successfully');
        }
        return $this->getResponse(true, $plans, 'Get data successfully', $paginate);
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
                'message' => 'Cannot get data'
            ]);
        }
        $plan = $this->planRepo->getPlanById($id);
        if (!$plan) {
            return $this->getResponse(false, [
                'message' => 'Element does not exist'
            ]);
        }
        return $this->getResponse(true, $plan, 'Get data successfully');
    }

    /**
     * Controller create a new plan
     * @return $this|BaseController|\Illuminate\Http\JsonResponse
     */
    public function createPlan()
    {
        $validation = Validator::make($this->data,
            ['plan_name' => 'required'],
            ['plan_name.required' => 'Plan name is required']
        );
        if ($validation->fails()) {
            return $this->checkValidate($validation);
        }

        $plan = $this->planRepo->createPlan($this->data);
        if (!$plan) {
            return $this->getResponse(false, [
                'message' => 'Cannot create data'
            ]);
        }
        return $this->getResponse(true, $plan, 'Create plan successfully');
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
                'message' => 'Cannot update data'
            ]);
        };
        $item = $this->planRepo->updatePlan($this->data, $id);
        if (!$item) return $this->getResponse(false, [
            'message' => 'Cannot update data!'
        ]);
        return $this->getResponse(true, $item, 'Update plan successfully');

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
                'message' => 'Cannot delete data'
            ]);
        }
        $item = $this->planRepo->deletePlan($id);
        if (!$item) {
            return $this->getResponse(false, ['code' => '', 'message' => 'Cannot delete data!']);
        }
        return $this->getResponse(true, (object)[], 'Delete data successfully');
    }
}