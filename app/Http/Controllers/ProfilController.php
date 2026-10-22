<?php

namespace App\Http\Controllers;

use App\Http\Requests\GantiPasswordRequest;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    // Halaman profil + form ganti password (semua role)
    public function edit()
    {
        return view('profil.edit', ['user' => auth()->user()]);
    }

    public function updatePassword(GantiPasswordRequest $request)
    {
        // Selalu akun yang sedang login, tidak menerima id dari request
        $user = $request->user();

        // Disimpan dalam bentuk hash (bcrypt), bukan teks biasa
        $user->forceFill([
            'password' => Hash::make($request->password_baru),
        ])->save();

        $request->session()->regenerate();

        return redirect()
            ->route($user->role . '.profil.edit')
            ->with('success', 'Password berhasil diganti.');
    }
}
