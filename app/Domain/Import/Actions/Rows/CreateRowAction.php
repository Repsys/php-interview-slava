<?php

namespace App\Domain\Import\Actions\Rows;

use App\Domain\Import\Models\Row;

class CreateRowAction
{
    public function execute(array $fields): Row
    {
        // Записи с одинаковым id добавляются только один раз
        if ($row = Row::find($fields['id'])) {
            $row->fill($fields);
        } else {
            $row = new Row($fields);
            $row->id = $fields['id'];
        }

        $row->save();

        return $row;
    }
}
