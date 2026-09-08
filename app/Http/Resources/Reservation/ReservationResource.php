<?php

namespace App\Http\Resources\Reservation;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'customer'              => $this->customer,
            'date'                  => $this->date,
            'hour'                  => $this->hour,
            'quantity_of_person'    => $this->quantity_of_person,
            'state_registration'    => $this->state_registration,
            'phone'                 => $this->phone,
            'email'                 => $this->email,
            'observation'           => $this->observation,
            'buffer_time'          => $this->buffer_time,
            'table' => [
                'id'                => $this->table->id,
                'number'            => $this->table->number,
                'name'              => $this->table->name
            ],
            'status'    => [
                'value'             => $this->status->value,
                'label'             => $this->status->getLabel(),
                'severity'          => $this->status->getSeverity(),
                'label_severity'    => $this->status->getLabelSeverity()
            ],
            'duration'              => $this->duration,
            'waiter'    => $this->when($this->waiter,[
                'id'                => $this->waiter?->id,
                'name'              => $this->waiter?->name
            ]),
        ];
    }
}
