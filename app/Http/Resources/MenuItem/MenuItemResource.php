<?php

namespace App\Http\Resources\MenuItem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class MenuItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"    => $this->id,
            "code"  => $this->code,
            "name"  => $this->name,
            "description"   => $this->description,
            "image" => $this->image,
            "price" => [
                "label" => $this->getPrice(),
                "value" => $this->price
            ],
            "promotional_price" => [
                "label" => $this->getCurrency("promotional_price"),
                "value" => $this->promotional_price
            ],
            "category"  => [
                "id"    => $this->category->id,
                "name"  => $this->category->name
            ],
            "is_active" => $this->is_active,
            "featured_types"            => $this->featured_types,
            "enable_technical_sheet"    => [
                "label"     => $this->enableTechnicalSheetLabel(),
                "value"     => $this->enable_technical_sheet
            ]
        ];
    }
}
