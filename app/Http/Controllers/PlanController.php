<?php

namespace App\Http\Controllers;

use App\Http\Base\BaseController;
use App\Jobs\CreatePlanJob;
use App\Models\Plan;
use App\Repositories\PlanRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;


class PlanController extends BaseController
{
    protected $data;
    protected $planRepo;
    protected $redis;

    public function __construct(Request $request, Plan $plan, PlanRepository $planRepository, Redis $redis)
    {
        $this->data     = $request->all(); // Get all request
        $this->planRepo = $planRepository;
        $this->redis    = $redis::connection('plan'); //Create redis connection
    }

    /**
     * Controller get all plans
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function getAllPlans($page = 1, $limit = 10)
    {
        try {
            if (!empty($this->data['page'])) {
                $page = $this->data['page'];
            }
            if (!empty($this->data['limit'])) {
                $limit = $this->data['limit'];
            }
            $plansRedisKey = 'list:' . $page . ':' . $limit;
            if ($this->redis->exists($plansRedisKey)) {      //Check get all plans key is existed on redis
                $plans = json_decode($this->redis->get($plansRedisKey));
                return $this->getResponse(true, $plans, 'Get data successfully');
            }
            $plans    = $this->planRepo->getAllPlans($page, $limit);
            $paginate = $this->planRepo->getPagination($page, $limit);
            if (empty($plans)) {
                return $this->getResponse(true, [], 'Get data successfully');
            }
            $this->redis->set($plansRedisKey, $plans);
            return $this->getResponse(true, $plans, 'Get data successfully', $paginate);
        } catch (\Exception $e) {
            return $this->catchResponseException($e);
        }
    }

    /**
     * Controller get plan by id
     * @param $id
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function getPlanById($id)
    {
        try {
            if (empty($id)) {
                return $this->getResponse(false, [
                    'message' => 'Cannot get data'
                ]);
            }

            if ($this->redis->exists('plan:' . $id)) {
                $plan = json_decode($this->redis->get('plan:' . $id));
                return $this->getResponse(true, $plan, 'Get data successfully');
            }

            $plan = $this->planRepo->getPlanById($id);
            if (!$plan) {
                return $this->getResponse(false, [
                    'message' => 'Element does not exist'
                ]);
            }
            $this->redis->set('plan:' . $id, $plan);
            return $this->getResponse(true, $plan, 'Get data successfully');
        } catch (\Exception $e) {
            return $this->catchResponseException($e);
        }
    }

    /**
     * Controller create a new plan
     * @return $this|BaseController|\Illuminate\Http\JsonResponse
     */
    public function createPlan()
    {
        try {
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
            $this->deleteListPlanKeys(); //Delete all plan lists on redis
            return $this->getResponse(true, $plan, 'Create plan successfully', [],201);
        } catch (\Exception $e) {
            return $this->catchResponseException($e);
        }
    }

    /**
     * Controller update info of a plan
     * @param $id
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function updatePlan($id)
    {
        try {
            if (empty($id) || empty($this->data)) {
                return $this->getResponse(false, [
                    'message' => 'Cannot update data'
                ]);
            };
            $item = $this->planRepo->updatePlan($this->data, $id);
            if (!$item) return $this->getResponse(false, [
                'message' => 'Cannot update data!'
            ]);
            if ($this->redis->exists('plan:' . $id)) { //Check and delete redis key of current plan
                $this->redis->del(['plan:' . $id]);
            }
            $this->deleteListPlanKeys(); //Delete all plan lists on redis
            return $this->getResponse(true, $item, 'Update plan successfully');
        } catch (\Exception $e) {
            return $this->catchResponseException($e);
        }
    }

    /**
     * Controller delete a plan (update status 1 -> 0)
     * @param $id
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function deletePlan($id)
    {
        try {
            if (empty($id)) {
                return $this->getResponse(false, [
                    'message' => 'Cannot delete data'
                ]);
            }
            $item = $this->planRepo->deletePlan($id);
            if (!$item) {
                return $this->getResponse(false, ['code' => '', 'message' => 'Cannot delete data!']);
            }
            if ($this->redis->exists('plan:' . $id)) {  //Check and delete redis key of current plan
                $this->redis->del(['plan:' . $id]);
            }
            $this->deleteListPlanKeys(); //Delete all plan lists on redis
            return $this->getResponse(true, (object)[], 'Delete data successfully');
        } catch (\Exception $e) {
            return $this->catchResponseException($e);
        }
    }

    /**
     * Function get and delete all get-all-plans redis keys
     * @return bool
     */
    public function deleteListPlanKeys()
    {
        $redisKeys = $this->redis->keys('list:*'); //Get all redis keys start with 'list:'
        if (empty($redisKeys)) {
            return true;
        }
        foreach ($redisKeys as $key) {
            $this->redis->del($key);
        }
        return true;
    }

    /**
     * Function create plan and put into queue
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function createPlanWithQueue()
    {
        $jobCreatePlan = (new CreatePlanJob($this->data, $this->planRepo))->onQueue('plan')->delay(Carbon::now()->addMinutes(3));
        dispatch($jobCreatePlan);
        return $this->getResponse(true, (object)[], 'Create plan in queue successfully');
    }
}