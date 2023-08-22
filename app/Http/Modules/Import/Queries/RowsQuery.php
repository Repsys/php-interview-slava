<?php

namespace App\Http\Modules\Import\Queries;

use App\Domain\Import\Models\Row;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class RowsQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        parent::__construct(Row::query());

        $this->allowedSorts([
            'id',
            'name',
            'date',
            'created_at',
            'updated_at',
        ]);
        $this->defaultSort('id');

        $this->allowedFilters([
            AllowedFilter::exact('id'),
            AllowedFilter::exact('name'),
            AllowedFilter::partial('name_like', 'name'),
        ]);
    }
}
