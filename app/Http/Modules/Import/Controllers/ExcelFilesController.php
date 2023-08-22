<?php

namespace App\Http\Modules\Import\Controllers;

use App\Domain\Import\Actions\ExcelFiles\CreateExcelFileAction;
use App\Domain\Import\Actions\ExcelFiles\DeleteExcelFileAction;
use App\Domain\Import\Actions\ExcelFiles\RetryImportExcelFileAction;
use App\Http\Controllers\Controller;
use App\Http\Modules\Import\Queries\ExcelFilesQuery;
use App\Http\Modules\Import\Requests\CreateExcelFileRequest;
use App\Http\Modules\Import\Resources\ExcelFileResource;
use App\Http\Support\Pagination\PageBuilderFactory;
use App\Http\Support\Resources\EmptyResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExcelFilesController extends Controller
{
    public function create(CreateExcelFileRequest $request, CreateExcelFileAction $action): ExcelFileResource
    {
        $file = $request->file('file');
        return ExcelFileResource::make($action->execute($file));
    }

    public function get(int $id, ExcelFilesQuery $query): ExcelFileResource
    {
        return ExcelFileResource::make($query->findOrFail($id));
    }

    public function delete(int $id, DeleteExcelFileAction $action): EmptyResource
    {
        $action->execute($id);

        return new EmptyResource();
    }

    public function retryImport(int $id, RetryImportExcelFileAction $action): EmptyResource
    {
        $action->execute($id);

        return new EmptyResource();
    }

    public function search(PageBuilderFactory $pageBuilderFactory, ExcelFilesQuery $query): AnonymousResourceCollection
    {
        return ExcelFileResource::collectPage(
            $pageBuilderFactory->fromQuery($query)->build()
        );
    }
}
