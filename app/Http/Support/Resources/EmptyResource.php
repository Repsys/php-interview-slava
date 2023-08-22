<?php

namespace App\Http\Support\Resources;

use Illuminate\Contracts\Support\Responsable;
use Symfony\Component\HttpFoundation\Response;

class EmptyResource implements Responsable
{
    public function toResponse($request): Response
    {
        return response()->json(['data' => null]);
    }
}
