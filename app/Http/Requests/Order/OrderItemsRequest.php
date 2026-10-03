<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderItemsRequest extends FormRequest
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
           "items"                                  => "required|array",
           "items.*.order_item_id"                  => "nullable|integer",
           "items.*.menu_item_id"                   => "required|integer",
           "items.*.quantity"                       => "required|integer",
           "items.*.unit_price"                     => "nullable|numeric",
           "items.*.side_dishes"                    => "nullable|array",
           "items.*.side_dishes.*.menu_item_id"     => "required|integer",
           "items.*.side_dishes.*.quantity"         => "required|integer",
        ];
    }
}
