<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
             
            'userId'     => 'required|integer',
            'appId'      => 'required|integer|in:'.config("global.APP_ID"),
            'access'     => 'required|string|size:8',
            'couponCode' => 'required|string|max:8',
            'mobile_number' => 'reuquired|digit:10'
        ];
            //
        
    }

     public function messages()
    {
        return [
            'userId.required'     => 'User ID is required.',
            'userId.integer'      => 'User ID must be a valid integer.',

            'appId.required'      => 'App ID is required.',
            'appId.integer'       => 'App ID must be a number.',
            'appId.in'            => 'App ID must be 1.',

            'access.required'     => 'Access key is required.',
            'access.string'       => 'Access key must be a string.',
            'access.size'         => 'Access key must be exactly 8 characters.',

            'couponCode.required' => 'Coupon code is required.',
            'couponCode.string'   => 'Coupon code must be a string.',
            'couponCode.max'      => 'Coupon code cannot be more than 8 characters.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
