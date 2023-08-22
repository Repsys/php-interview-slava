<?php

namespace App\Http\Support\Pagination;

enum PaginationTypeEnum : string
{
    case CURSOR = 'cursor';
    case OFFSET = 'offset';
}
