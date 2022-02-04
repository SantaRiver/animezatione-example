<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class     NFTUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'string|required',
            'name' => 'string',
            'assets' => 'string',
            'rarity' => 'string|nullable',
            'benefit' => 'string|nullable',
            'burning' => 'string|nullable',
            'description' => 'string|nullable',
            'active' => 'boolean',
        ];
    }
}
