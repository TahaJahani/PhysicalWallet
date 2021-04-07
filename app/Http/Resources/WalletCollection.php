<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class WalletCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $totalValue = 0;
        foreach ($this->collection as $wallet)
            $totalValue += $wallet->value;
        return [
            'total_value' => $totalValue,
            'wallets' => $this->collection,
        ];
    }
}
