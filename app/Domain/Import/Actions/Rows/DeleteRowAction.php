<?php

namespace App\Domain\Import\Actions\Rows;

use App\Domain\Import\Models\Row;

class DeleteRowAction
{
    public function execute(int $id): void
    {
        Row::destroy($id);
    }
}
