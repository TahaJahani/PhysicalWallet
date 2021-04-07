<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class WalletNotFoundException extends Exception
{
    /**
     * the message that should be returned
     * when the exception is thrown
     *
     * @return JsonResponse
     */
    public function render () {
        return response()->json(['error' => 'کیف پول مورد نظر پیدا نشد'], 400);
    }
}
