<?php

namespace App\Domain\Import\Enums;

/**
 * Статус импорта ExcelFile
 */
enum ExcelFileStatusEnum: string
{
    case NEW = 'new';
    case RUNNING = 'running';
    case COMPLETED = 'completed';
    case ERROR = 'error';
}
