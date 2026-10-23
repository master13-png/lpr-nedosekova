<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (int) $this->id,
            'manufacturerId' => (int) $this->manufacturer_id,
            'code' => $this->code,
            'name' => $this->name,
            'form' => $this->form,
            'dosageMg' => (int) $this->dosage_mg,
            'price' => (float) $this->price,
            'byPrescription' => $this->by_prescription,
            'manufacturer' => $this->whenLoaded('manufacturer', fn (): array => [
                'id' => $this->manufacturer->id,
                'name' => $this->manufacturer->name,
                'country' => $this->manufacturer->country,
            ]),
            'createdAt' => $this->created_at?->toIso8601String(),
            'updatedAt' => $this->updated_at?->toIso8601String(),
        ];
    }
}
