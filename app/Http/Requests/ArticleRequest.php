<?php

namespace App\Http\Requests;

class ArticleRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'name' => 'required|min:6', // at least 6 characters required
            'author' => 'required', // required, not empty
            'created_at' => 'required|date' // required and date format
        ];
    }

}
