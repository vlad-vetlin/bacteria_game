<?php

namespace App\Services;


use DB;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;


class Paginator
{
    /**
     * @param Collection|QueryBuilder|EloquentBuilder $data
     * @param int $per_page
     * @param int $current_page
     * @return LengthAwarePaginator
     */
    public static function paginate($data, $per_page, $current_page)
    {
        // fix for groupBy methods
        if ($data instanceof Collection) {
            $count = $data->count();
            $res = $data->forPage($current_page, $per_page)->values()->all();
        } else {
            /* @var QueryBuilder $db_query_builder */
            $db_query_builder = $data instanceof EloquentBuilder ? $data->getQuery() : $data;

            $count = DB::table(DB::raw("({$data->toSql()}) as sub"))
                ->mergeBindings($db_query_builder)
                ->count();
            $res = $data->forPage($current_page, $per_page)->get();
        }

        return new LengthAwarePaginator(
            $res,
            $count,
            $per_page,
            $current_page,
            [
                "path" => "/" . request()->path() . "?" . http_build_query(request()->except("page")),
            ]
        );
    }

    public static function paginateIfNeeded($data)
    {
        $current_page = request("pagination.page", 1);
        $per_page = request("pagination.per_page", 15);

        return self::paginate($data, $per_page, $current_page);
    }
}
