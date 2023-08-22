<?php

namespace App\Http\Modules\Import\Resources;

use App\Domain\Import\Models\ExcelFile;
use App\Http\Support\Resources\BaseJsonResource;

/**
 * @mixin ExcelFile
 */
class ExcelFileResource extends BaseJsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'path' => $this->path,
            'original_name' => $this->original_name,
            'imported_count' => $this->imported_count,
            'imported_count_dynamic' => $this->getDynamicImportedCount(),
            'status' => $this->status,
            'error_message' => $this->error_message,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
