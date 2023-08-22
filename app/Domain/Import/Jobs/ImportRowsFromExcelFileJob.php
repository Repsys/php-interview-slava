<?php

namespace App\Domain\Import\Jobs;

use App\Domain\Import\Actions\Rows\CreateRowAction;
use App\Domain\Import\Enums\ExcelFileStatusEnum;
use App\Domain\Import\Models\ExcelFile;
use App\Domain\Support\Jobs\AbstractJob;
use Illuminate\Bus\Batchable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;
use Illuminate\Redis\Connections\Connection as RedisConnection;
use Illuminate\Redis\Connections\PredisConnection;
use Illuminate\Support\Facades\Redis;
use Throwable;

class ImportRowsFromExcelFileJob extends AbstractJob
{
    use Batchable;

    const CHUNK_SIZE = 100;

    /** @var PredisConnection */
    private RedisConnection $redis;
    private string $rkImportedCount;

    private CreateRowAction $createRowAction;

    private int $currentRow = 0;

    public function __construct(
        private readonly ExcelFile $excelFile
    )
    {
    }

    public function init()
    {
        $this->redis = Redis::resolve();
        $this->rkImportedCount = $this->excelFile->getRKImportedCount();
        $this->currentRow = $this->excelFile->imported_count;
        $this->updateRedisImportedCount();

        $this->createRowAction = new CreateRowAction();
    }

    public function execute()
    {
        if ($this->excelFile->status != ExcelFileStatusEnum::RUNNING) {
            $this->excelFile->update([
                'status' => ExcelFileStatusEnum::RUNNING,
                'error_message' => null,
            ]);
        }

        $this->log("Job started for the Excel File with id={$this->excelFile->id}");

        $status = $this->importRows();

        $this->excelFile->update([
            'status' => $status,
            'imported_count' => $this->currentRow,
        ]);

        if ($status == ExcelFileStatusEnum::COMPLETED) {
            $this->log("Import completed on " . $this->currentRow . " rows");
        }
        elseif ($status == ExcelFileStatusEnum::RUNNING) {
            $this->batch()->add([
                new ImportRowsFromExcelFileJob($this->excelFile),
            ]);
        }
    }

    public function handleException(Throwable $exception)
    {
        $this->excelFile->update([
            'imported_count' => $this->currentRow,
            'status' => ExcelFileStatusEnum::ERROR,
            'error_message' => $exception->getMessage(),
        ]);
    }

    public function importRows(): ExcelFileStatusEnum
    {
        $rows = $this->excelFile->readRows()
            ->slice($this->currentRow, self::CHUNK_SIZE)
            ->collect(); // Без collect() - метод count() странно себя ведет и перемещает указатель на следующий чанк

        $rowsCount = $rows->count();
        $lastRow = $this->currentRow + $rowsCount;
        $this->log("Import rows from {$this->currentRow} to {$lastRow}. Total = {$rowsCount}");

        $rows->each(function (array $fields) {
            $this->createRowAction->execute($fields);

            $this->currentRow++;
            $this->updateRedisImportedCount();
        });

        if ($rowsCount < self::CHUNK_SIZE) {
            return ExcelFileStatusEnum::COMPLETED;
        }

        return ExcelFileStatusEnum::RUNNING;
    }

    private function updateRedisImportedCount()
    {
        $this->redis->set($this->rkImportedCount, $this->currentRow);
    }

    public function middleware(): array
    {
        return [new SkipIfBatchCancelled];
    }
}
