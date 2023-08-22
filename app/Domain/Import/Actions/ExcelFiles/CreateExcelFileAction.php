<?php

namespace App\Domain\Import\Actions\ExcelFiles;

use App\Domain\Import\Jobs\ImportRowsFromExcelFileJob;
use App\Domain\Import\Models\ExcelFile;
use App\Exceptions\BusinessErrorException;
use App\Support\FileHelper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;

class CreateExcelFileAction
{
    public function execute(UploadedFile $file): ExcelFile
    {
        $originalFullName = $file->getClientOriginalName();
        $originalName = pathinfo($originalFullName, PATHINFO_FILENAME);
        $extension = pathinfo($originalFullName, PATHINFO_EXTENSION);

        $targetName = FileHelper::generateRandomName($originalName, $extension);
        $path = Storage::disk('local')->putFileAs(ExcelFile::FILES_PATH, $file, $targetName);

        $excelFile = new ExcelFile();
        $excelFile->path = $path;
        $excelFile->original_name = $originalName;

        $this->validateFile($excelFile);

        $excelFile->save();

        Bus::batch([
            new ImportRowsFromExcelFileJob($excelFile),
        ])->dispatch();

        return $excelFile;
    }

    public function validateFile(ExcelFile $excelFile): void
    {
        $headers = $excelFile->openExcelReader()->getHeaders();

        if (!empty(array_diff(['id', 'name', 'date'], $headers))) {
            throw new BusinessErrorException("Headers (id, name, date) are not represented in excel file");
        }
    }
}
