<?php

namespace App\Http\Support\Pagination;

use Illuminate\Support\Collection;

class Page
{
    public array $items;

    public function __construct(array|Collection $items, public array $pagination)
    {
        $this->items = is_object($items) ? $items->all() : $items;
    }
}
