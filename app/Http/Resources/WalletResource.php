<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'value' => $this->value,
            'created_at' => $this->created_at->toRfc7231String(),
            'last_transaction_at' => $this->updated_at->toRfc7231String(),
        ];
    }
}
