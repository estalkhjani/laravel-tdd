<?php

namespace App\Http\Requests;

use App\Author;
use Illuminate\Foundation\Http\FormRequest;

class PostBookRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'isbn'=>'required|string|unique|min:13|max:13',
            'title'=>'required|string|max:250',
            'description'=>'required|string',
            'authors'=>'required|array',
            'authors.*'=>'required|int|exists:'.Author::class.',id',
        ];
    }
}
