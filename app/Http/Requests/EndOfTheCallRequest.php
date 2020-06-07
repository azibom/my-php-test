<?php

namespace App\Http\Requests;

use App\Rules\PriorityChecker;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class EndOfTheCallRequest extends FormRequest {
    const UNPROCESSABLE_ENTITY = 422;
    
    public function rules() {
        return [
            'hash' => ['required']
          ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json($validator->errors(), self::UNPROCESSABLE_ENTITY));
    }
}