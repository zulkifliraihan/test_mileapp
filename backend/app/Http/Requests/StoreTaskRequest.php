<?php

namespace App\Http\Requests;

use App\Traits\ReturnResponser;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTaskRequest extends FormRequest
{
    use ReturnResponser;
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'description' => 'nullable|string',
            'status' => 'required|string|in:open,in_progress,done',
            'due_date' => 'nullable|date',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->errorvalidator($this->validator->errors()->messages()));
    }
}
