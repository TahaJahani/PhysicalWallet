<?php

namespace App\Exceptions;

use Exception;

class WalletNotFoundException extends Exception
{
    public function render () {
        return response()->json(['error' => 'کیف پول مورد نظر پیدا نشد'], 400);
    }
}
