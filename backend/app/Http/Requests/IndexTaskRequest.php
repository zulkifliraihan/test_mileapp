<?php

namespace App\Http\Requests;

use App\Traits\ReturnResponser;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class IndexTaskRequest extends FormRequest
{
    use ReturnResponser;
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['required', 'integer', 'min:1', 'max:100'],
            'page' => ['required', 'integer', 'min:1'],

            'filter' => ['sometimes', 'array'],
            'filter.status' => ['sometimes', 'string', 'in:open,in_progress,done'],
            'filter.title' => ['sometimes', 'string'],
            'filter.start_date' => ['sometimes', 'date'],
            'filter.end_date' => ['sometimes', 'date', 'after_or_equal:filter.start_date'],

            'sort' => ['sometimes', 'array'],
            'sort.field' => ['required_with:sort', 'in:title,status,due_date,created_at'],
            'sort.dir' => ['required_with:sort', 'in:asc,desc'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->errorvalidator($this->validator->errors()->messages()));
    }
}
