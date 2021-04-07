<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'value' => abs($this->value),
            'type' => $this->value > 0 ? 'deposit' : 'receive',
            'performed_at' => $this->created_at,
        ];
    }
}
