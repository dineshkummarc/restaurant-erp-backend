<?php

namespace App\Http\Requests\Reservation;

use Illuminate\Foundation\Http\FormRequest;

class ReservationCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer'              => 'required|string',
            'state_registration'    => 'nullable|string',
            'phone'                 => 'nullable|string',
            'email'                 => 'nullable|string',
            'date'                  => 'required|date',
            'hour'                  => 'required|string',
            'quantity_of_person'    => 'required|integer',
            'observation'           => 'nullable|string',
            'table_id'              => 'required|integer',
            'waiter_id'             => 'nullable|integer',
            'duration'              => 'nullable|string',
            'buffer_time'           => 'nullable|string',
        ];
    }
}
