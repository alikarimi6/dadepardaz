<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'status' => $this->status ,
            'rejection_comment' => $this->rejection_comment,
            'user' => optional($this->user)->name,
            'category' => optional($this->category)->name,
            'attachment' => $this->attachment->file_path??null
        ];
    }
}
