<?php

namespace App\Domain\Import\Actions\ExcelFiles;

use App\Domain\Import\Enums\ExcelFileStatusEnum;
use App\Domain\Import\Jobs\ImportRowsFromExcelFileJob;
use App\Domain\Import\Models\ExcelFile;
use App\Exceptions\BusinessErrorException;
use Illuminate\Support\Facades\Bus;

class RetryImportExcelFileAction
{
    public function execute(int $id): void
    {
        $excelFile = ExcelFile::findOrFail($id);

        if ($excelFile->status != ExcelFileStatusEnum::ERROR) {
            throw new BusinessErrorException("To retry import, the excel file must be in error status");
        }

        Bus::batch([
            new ImportRowsFromExcelFileJob($excelFile),
        ])->dispatch();
    }
}
