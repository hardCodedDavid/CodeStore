<?php

namespace App\Exceptions;

use App\Traits\RespondsWithHttpStatus;
use Exception;
use Illuminate\Http\JsonResponse;

class CustomException extends Exception
{
    use RespondsWithHttpStatus;

    public function render(): JsonResponse
    {
        return $this->failure($this->getMessage());
    }
}
