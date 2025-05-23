<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'room_id' => $this->room_id,
            'user_id' => $this->user_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'number_of_people' => $this->number_of_people,
            'nights' => $this->nights,
            'total' => (float) $this->total,
            'isPaid' => $this->isPaid,
            'isCanceled' => $this->isCanceled,
        ];
    }
}
