<?php

namespace App\Http\Modules\Import\Resources;

use App\Domain\Import\Models\Row;
use App\Http\Support\Resources\BaseJsonResource;

class RowGroupsResource extends BaseJsonResource
{
    public function toArray($request): array
    {
        $groups = [];
        /** @var Row $row */
        foreach ($this->resource as $row) {
            $groups[$row->date->format('m.d.Y')][] = RowResource::make($row);
        }

        return $groups;
    }
}
