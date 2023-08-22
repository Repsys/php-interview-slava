<?php

namespace App\Http\Support\Pagination;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder as SpatieQueryBuilder;

class PageBuilderFactory
{
    public function fromQuery(
        EloquentQueryBuilder|QueryBuilder|SpatieQueryBuilder $query,
        ?Request $request = null
    ): AbstractPageBuilder {
        $request = $request ?: resolve(Request::class);

        return $request->input('pagination.type') === PaginationTypeEnum::CURSOR->value
         ? new CursorPageBuilder($query, $request)
         : new OffsetPageBuilder($query, $request);
    }
}
