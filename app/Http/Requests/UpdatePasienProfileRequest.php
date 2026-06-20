<?php

namespace App\Http\Requests;

use App\Models\Pasien;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePasienProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        $pasien = $this->route('pasien');

        return $pasien instanceof Pasien
            && (int) $pasien->user_id === (int) $this->user()->id;
    }

    public function rules(): array
    {
        $pasien = $this->route('pasien');

        return [
            'nik' => [
                'required',
                'digits:16',
                Rule::unique(Pasien::class, 'nik')->ignore($pasien?->id),
            ],
            'nama_pasien' => ['required', 'string', 'max:255'],
            'tgl_lahir' => ['required', 'date', 'before:today'],
            'jenis_kelamin' => ['required', Rule::in(['Laki-laki', 'Perempuan'])],
            'alamat' => ['required', 'string', 'max:1000'],
            'no_hp' => ['required', 'string', 'max:20'],
        ];
    }
}
