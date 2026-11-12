<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFilmRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'sinopsis' => ['required', 'string', 'max:10000'],
            'year' => ['required', 'integer', 'min:1888', 'max:' . (now()->year + 10)],
            'poster' => ['required', 'string', 'max:2048'],
            'genre_id' => ['required', 'integer', 'exists:genres,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul film wajib diisi.',
            'sinopsis.required' => 'Sinopsis wajib diisi.',
            'year.required' => 'Tahun rilis wajib diisi.',
            'year.integer' => 'Tahun rilis harus berupa angka.',
            'poster.required' => 'Path poster wajib diisi.',
            'genre_id.required' => 'Genre wajib dipilih.',
            'genre_id.exists' => 'Genre yang dipilih tidak ditemukan.',
        ];
    }
}
