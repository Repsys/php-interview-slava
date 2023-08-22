<?php

namespace App\Domain\Import\Models;

use App\Domain\Import\Enums\ExcelFileStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Redis\Connections\PredisConnection;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use Spatie\SimpleExcel\SimpleExcelReader;

/**
 * App\Domain\Import\Models\ExcelFile
 *
 * @property int $id
 * @property string $path
 * @property string $original_name
 * @property int $imported_count
 * @property string $status
 * @property string|null $error_message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelFile query()
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelFile whereErrorMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelFile whereImportedCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelFile whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelFile wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelFile whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ExcelFile whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ExcelFile extends Model
{
    public const FILES_PATH = 'import/excel';
    public const RK_IMPORTED_COUNT_ = 'excel_file_imported_count_';

    protected $fillable = [
        'imported_count',
        'status',
        'error_message',
    ];

    protected $casts = [
        'status' => ExcelFileStatusEnum::class
    ];

    protected $attributes = [
        'imported_count' => 0,
        'status' => ExcelFileStatusEnum::NEW,
    ];

    public function getRKImportedCount(): string
    {
        return self::RK_IMPORTED_COUNT_ . $this->id;
    }

    public function getDynamicImportedCount(): int
    {
        /** @var PredisConnection $redis */
        $redis = Redis::resolve();

        $importedCount = $redis->get($this->getRKImportedCount()) ?? $this->imported_count;

        return (int)$importedCount;
    }

    public function openExcelReader(): SimpleExcelReader
    {
        $localFullPath = Storage::disk('local')->path($this->path);
        return SimpleExcelReader::create($localFullPath, 'xlsx')
            ->fromSheet(1)
            ->trimHeaderRow();
    }

    public function readRows(): LazyCollection
    {
        return $this->openExcelReader()->getRows();
    }
}
