<?php

use App\Http\Modules\Import\Controllers\RowsController;
use Illuminate\Support\Facades\Route;

Route::get('rows/{id}', [RowsController::class, 'get']);
Route::delete('rows/{id}', [RowsController::class, 'delete']);
Route::post('rows:search', [RowsController::class, 'search']);
