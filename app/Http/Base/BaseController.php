<?php

namespace App\Http\Base;

use App\Http\Controllers\Controller;
use App\Http\Models\Plan;
use Illuminate\Http\Request;

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
    public function getResponse($status = false, $data = [], $message = '', $paginate = [])
    {
        if ($status == false) {
            return response()->json([
                'status' => $status,
                'data'   => [
                    'error' => $data
                ]
            ]);
        }

        if (!empty($paginate)) {
            return response()->json([
                'status'     => $status,
                'data'       => $data,
                'message'    => $message,
                'pagination' => $paginate
            ])->setEncodingOptions(JSON_NUMERIC_CHECK);
        }

        return response()->json([
            'status'  => $status,
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

}
