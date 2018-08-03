<?php
namespace App\Http\Base;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * Function create pagination
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function getPagination($page = 1, $limit = 10){
        $totalResult = DB::table($this->table)->count();
        return [
            "page" => $page,
            "limit" => $limit,
            "total" => $totalResult
        ];
    }
}