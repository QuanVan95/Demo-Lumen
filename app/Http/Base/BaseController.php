<?php

namespace App\Http\Base;

use App\Http\Controllers\Controller;
use App\Http\Models\Plan;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BaseController extends Controller
{
    public function __construct(Request $request, Plan $plan)
    {
    }

    /**
     * Function format response for API
     * @param bool $status
     * @param array $data
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function getResponse($status = false, $data = [], $message = '', $paginate = [], $code = 200)
    {
        if ($status == false) {
            return response()->json([
                'status'  => $status,
                'code'    => $code,
                'message' => $message,
                'data'    => [
                    'error' => $data
                ]
            ]);
        }

        if (!empty($paginate)) {
            return response()->json([
                'status'     => $status,
                'code'       => $code,
                'data'       => $data,
                'message'    => $message,
                'pagination' => $paginate
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);
        }

        return response()->json([
            'status'  => $status,
            'code'    => $code,
            'data'    => $data,
            'message' => $message
        ]);
    }

    /**
     * Function check validation
     * @param $value
     * @return BaseController|\Illuminate\Http\JsonResponse
     */
    public function checkValidate($value)
    {
        $error = $value->messages()->all();
        return $this->getResponse(false, [
            'code'    => '',
            'message' => $error[0]
        ]);
    }

    /**
     * Function catch exception for response API
     * @return BaseController|\Illuminate\Http\JsonResponse
     */
    public function catchResponseException($e)
    {
        if ($e instanceof HttpException) {
            return $this->getResponse(false, (object)[], 'Something went wrong', [], $e->getStatusCode());
        }
        if ($e->getCode()) {
            return $this->getResponse(false, (object)[], 'Something went wrong', [], $e->getCode());
        }
        return $this->getResponse(false, (object)[], 'Something went wrong', [], 400);
    }

}
