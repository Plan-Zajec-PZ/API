<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexLecturersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'faculty' => ['nullable', 'int', 'exists:faculties,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'faculty' => $this->query('faculty'),
        ]);
    }
}
