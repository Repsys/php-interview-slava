<?php

namespace App\Domain\Import\Actions\ExcelFiles;

use App\Domain\Import\Enums\ExcelFileStatusEnum;
use App\Domain\Import\Models\ExcelFile;
use App\Exceptions\BusinessErrorException;
use Illuminate\Support\Facades\Storage;

class DeleteExcelFileAction
{
    public function execute(int $id): void
    {
        $excelFile = ExcelFile::find($id);

        if (is_null($excelFile)) {
            return;
        }

        if ($excelFile->status === ExcelFileStatusEnum::RUNNING) {
            throw new BusinessErrorException("Unable to delete the import task in the running status");
        }

        Storage::disk('local')->delete($excelFile->path);

        $excelFile->delete();
    }
}
