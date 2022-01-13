<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255']
        ];
    }

    /** バリデーションエラーのカスタム属性
    *
    * @return array
    */
    public function attributes(): array
    {
        return [
            'name' => '物品名',
        ];
    }
}
