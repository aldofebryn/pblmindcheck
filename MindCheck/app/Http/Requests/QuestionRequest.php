<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // atau cek session admin
    }

    public function rules(): array
    {
        $uniqueRule = 'unique:questions,nomor';
        if ($this->method() === 'PUT' || $this->method() === 'PATCH') {
            $uniqueRule .= ',' . $this->route('question')->id;
        }

        return [
            'nomor'    => ['required', 'integer', 'min:1', 'max:21', $uniqueRule],
            'teks_id'  => ['required', 'string', 'max:500'],
            'teks_en'  => ['required', 'string', 'max:500'],
            'subskala' => ['required', 'in:Depression,Anxiety,Stress'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nomor'    => 'Nomor urut',
            'teks_id'  => 'Pertanyaan (Indonesia)',
            'teks_en'  => 'Pertanyaan (English)',
            'subskala' => 'Subskala',
        ];
    }

    public function messages(): array
    {
        return [
            'nomor.unique' => 'Nomor urut sudah digunakan. Pilih nomor lain.',
            'nomor.max'    => 'Nomor urut maksimal 21 (karena DASS-21).',
        ];
    }
}