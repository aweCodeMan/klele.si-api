<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->hasVerifiedEmail();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => ['required'],
            'postType' => ['required', 'in:0,1'],
            'groupUuid' => ['required', 'exists:groups,uuid'],
            'markdown' => ['exclude_unless:postType,0', 'required', 'string'],
        ];
    }
}
