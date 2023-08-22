<?php

namespace App\Http\Modules\Import\Queries;

use App\Domain\Import\Models\ExcelFile;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ExcelFilesQuery extends QueryBuilder
{
    public function __construct(Request $request)
    {
        parent::__construct(ExcelFile::query());

        $this->allowedSorts([
            'id',
            'original_name',
            'imported_count',
            'status',
            'created_at',
            'updated_at',
        ]);
        $this->defaultSort('id');

        $this->allowedFilters([
            AllowedFilter::exact('id'),
            AllowedFilter::exact('original_name'),
            AllowedFilter::partial('original_name_like', 'original_name'),
            AllowedFilter::exact('status'),
        ]);
    }
}
