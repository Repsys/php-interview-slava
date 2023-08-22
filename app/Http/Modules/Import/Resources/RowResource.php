<?php

namespace App\Http\Modules\Import\Resources;

use App\Domain\Import\Models\Row;
use App\Http\Support\Resources\BaseJsonResource;

/**
 * @mixin Row
 */
class RowResource extends BaseJsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'date' => $this->date->format('d.m.Y'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
