<?php

namespace App\Http\Modules\Import\Controllers;

use App\Domain\Import\Actions\Rows\DeleteRowAction;
use App\Domain\Import\Models\Row;
use App\Http\Controllers\Controller;
use App\Http\Modules\Import\Queries\RowsQuery;
use App\Http\Modules\Import\Resources\RowGroupResource;
use App\Http\Modules\Import\Resources\RowGroupsResource;
use App\Http\Modules\Import\Resources\RowResource;
use App\Http\Support\Pagination\PageBuilderFactory;
use App\Http\Support\Resources\EmptyResource;

class RowsController extends Controller
{
    public function get(int $id, RowsQuery $query): RowResource
    {
        return RowResource::make($query->findOrFail($id));
    }

    public function delete(int $id, DeleteRowAction $action): EmptyResource
    {
        $action->execute($id);

        return new EmptyResource();
    }

    public function search(PageBuilderFactory $pageBuilderFactory, RowsQuery $query)//: AnonymousResourceCollection
    {
        return RowResource::collectPage(
            $pageBuilderFactory->fromQuery($query)->build()
        );
    }

    public function group(): RowGroupsResource
    {
        $rows = Row::orderBy('date')->get();

        return RowGroupsResource::make($rows);
    }
}
