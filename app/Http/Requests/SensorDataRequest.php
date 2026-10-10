<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SensorDataRequest extends FormRequest
{
    /**
     * Tentukan apakah user diizinkan melakukan request ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'suhu' => ['required', 'numeric', 'min:0', 'max:60'],
            'kelembapan_udara' => ['required', 'numeric', 'min:0', 'max:100'],
            'kelembapan_tanah' => ['required', 'integer', 'min:0', 'max:4095'],
            'relay_fan' => ['nullable', 'boolean'],
            'relay_pump' => ['nullable', 'boolean'],
            'led_mode' => ['nullable', 'in:normal,kipas,pompa,kritis'],
        ];
    }

    /**
     * Pesan error dalam Bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'suhu.required' => 'Suhu wajib diisi',
            'suhu.numeric' => 'Suhu harus berupa angka',
            'suhu.min' => 'Suhu minimal 0°C',
            'suhu.max' => 'Suhu maksimal 60°C',
            'kelembapan_udara.required' => 'Kelembapan udara wajib diisi',
            'kelembapan_udara.numeric' => 'Kelembapan udara harus berupa angka',
            'kelembapan_tanah.required' => 'Kelembapan tanah wajib diisi',
            'kelembapan_tanah.integer' => 'Kelembapan tanah harus berupa bilangan bulat',
            'kelembapan_tanah.min' => 'Nilai ADC minimal 0',
            'kelembapan_tanah.max' => 'Nilai ADC maksimal 4095',
            'led_mode.in' => 'Mode LED tidak valid',
        ];
    }
}
