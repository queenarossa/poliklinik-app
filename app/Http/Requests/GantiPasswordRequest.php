<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GantiPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya pengguna yang sedang login, dan yang diubah selalu akunnya sendiri
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            // current_password: harus cocok dengan password akun yang sedang login
            'password_lama' => ['bail', 'required', 'current_password'],

            // minimal 8 karakter dan tidak boleh sama dengan password lama
            'password_baru' => ['bail', 'required', 'string', 'min:8', 'different:password_lama'],

            // harus sama dengan password baru
            'konfirmasi_password' => ['bail', 'required', 'same:password_baru'],
        ];
    }

    public function messages(): array
    {
        return [
            'password_lama.required'         => 'Password lama wajib diisi.',
            'password_lama.current_password' => 'Password lama tidak cocok dengan password akun Anda.',

            'password_baru.required'  => 'Password baru wajib diisi.',
            'password_baru.string'    => 'Password baru harus berupa teks.',
            'password_baru.min'       => 'Password baru minimal 8 karakter.',
            'password_baru.different' => 'Password baru tidak boleh sama dengan password lama.',

            'konfirmasi_password.required' => 'Konfirmasi password wajib diisi.',
            'konfirmasi_password.same'     => 'Konfirmasi password tidak sama dengan password baru.',
        ];
    }
}
