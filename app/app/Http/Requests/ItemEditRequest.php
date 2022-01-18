<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\IsEditable;

class ItemEditRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'id' => [new IsEditable()],
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

    /**
     * ルート引数のidは対象にならないのでマージする
     *
     * @return array
     */
    public function validationData(): array
    {
        return array_merge($this->all(), [
            'id' => $this->id,
        ]);
    }
}
