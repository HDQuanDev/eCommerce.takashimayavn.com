<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;


class CommisionPackageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [];
        $action = $this->route()->getActionMethod();
        match ($action) {
            'store' => $rules = [
                'name' => 'required|max:255',
                'price' => 'required|numeric|gt:0',
                'duration' => 'required|numeric|min:1',
                'commission_percentage' => 'required|numeric|between:0,100',
                'description' => 'nullable|string',
                'image' => 'nullable|numeric|exists:uploads,id',
                'status' => 'required|in:active,inactive',
            ],
            'update' => $rules = [
                'name' => 'required|max:255',
                'price' => 'required|numeric|gt:0',
                'duration' => 'required|numeric|gt:0',
                'commission_percentage' => 'required|numeric|between:0,100',
                'description' => 'nullable|string',
                'image' => 'nullable|numeric|exists:uploads,id',
                'status' => 'required|in:active,inactive',
            ],
        };
        return $rules;
    }

    public function failedValidation(Validator $validator)
    {
        // dd($this->expectsJson());
        if ($this->expectsJson()) {
            throw new HttpResponseException(response()->json([
                'message' => $validator->errors()->all(),
                'result' => false
            ], 422));
        } else {
            throw (new ValidationException($validator))
                ->errorBag($this->errorBag)
                ->redirectTo($this->getRedirectUrl());
        }
    }
}
