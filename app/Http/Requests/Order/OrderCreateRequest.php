<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderCreateRequest extends FormRequest
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
            "waiter_id"             => "nullable|integer",
            "table_id"              => "required|integer",
            "customer_name"         => "nullable|string",
            "status"                => "nullable|integer",
            "payment_status"        => "nullable|integer",
            "payment_method"        => "nullable|integer",
            "observation"           => "nullable|string",
            "customers_quantity"    => "nullable|integer",
            "reservation_id"        => "nullable|integer",
            ...OrderItemsRequest::capture()->rules()
        ];
    }
}
