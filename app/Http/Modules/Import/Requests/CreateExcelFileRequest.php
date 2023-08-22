<?php

namespace App\Http\Modules\Import\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class CreateExcelFileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => ['required', File::types(['xlsx'])->max(10240)],
        ];
    }
}
