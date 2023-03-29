<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DataTableService
{
    protected $query;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public static function of(Builder $query)
    {
        return new static($query);
    }

    public function applyFilters(Request $request)
    {
        if ($request->has('filter')) {
            $filter = json_decode($request->input('filter'));

            foreach ($filter as $field => $value) {
                $this->query->where($field, 'like', "%{$value}%");
            }
        }

        return $this;
    }

    public function applySorting(Request $request)
    {
        if ($request->has('sortField') && $request->has('sortOrder')) {
            $this->query->orderBy($request->input('sortField'), $request->input('sortOrder'));
        }

        return $this;
    }

    public function paginate(Request $request, $perPage = 10)
    {
        $page = $request->input('page', 1);
        $results = $this->query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $results->items(),
            'total' => $results->total(),
            'perPage' => $perPage,
            'currentPage' => $page,
            'lastPage' => $results->lastPage(),
            'sortField' => $request->input('sortField'),
            'sortOrder' => $request->input('sortOrder')
        ];
    }

    public function make(Request $request, $perPage = 10)
    {
        return $this->applyFilters($request)
            ->applySorting($request)
            ->paginate($request, $perPage);
    }
}
