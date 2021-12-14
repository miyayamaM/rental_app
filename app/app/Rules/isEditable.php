<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\Rental;

class IsEditable implements Rule
{
     /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Rental::whereNull('deleted_at')->where('item_id', $value)->doesntExist();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '貸し出し中の物品は操作できません。';
    }
}
