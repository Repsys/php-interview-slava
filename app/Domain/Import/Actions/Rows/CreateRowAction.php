<?php

namespace App\Domain\Import\Actions\Rows;

use App\Domain\Import\Models\Row;

class CreateRowAction
{
    public function execute(array $fields): Row
    {
        $row = Row::findOrNew($fields['id']);
        $row->name = $fields['name'];
        $row->date = $fields['date'];
        $row->save();

        return $row;
    }
}
