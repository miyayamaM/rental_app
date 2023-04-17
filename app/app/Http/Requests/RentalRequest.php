<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\IsRentable;

class RentalRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'item_id' => ['required', 'int', 'exists:items,id', new IsRentable()],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today']
        ];
    }
}
