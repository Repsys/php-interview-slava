<?php

use App\Http\Modules\Import\Controllers\ExcelFilesController;
use Illuminate\Support\Facades\Route;

Route::post('excel-files', [ExcelFilesController::class, 'create']);
Route::get('excel-files/{id}', [ExcelFilesController::class, 'get']);
Route::delete('excel-files/{id}', [ExcelFilesController::class, 'delete']);
Route::post('excel-files/{id}:retry-import', [ExcelFilesController::class, 'retryImport']);
Route::post('excel-files:search', [ExcelFilesController::class, 'search']);
