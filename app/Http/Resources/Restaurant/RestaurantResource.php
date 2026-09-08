<?php

namespace App\Http\Resources\Restaurant;

use App\Http\Resources\Address\AddressResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
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
            'name' => $this->name,
            'corporate_name' => $this->corporate_name,
            'description' => $this->description,
            'phone' => $this->phone,
            'email' => $this->email,
            'corporate_registration' => $this->corporate_registration,
            'loss_margim' => $this->loss_margim,
            'fix_margim' => $this->fix_margim,
            'variable_margim' => $this->variable_margim,
            'enable_tecnhical_sheet' => $this->enable_tecnhical_sheet,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'logo' => $this->logo,
            'reservation_buffer_time' => $this->reservation_buffer_time,
            'address' => new AddressResource($this->address),
        ];
    }
}
