<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    // Semua query lewat relasi milik user yang login, jadi tidak mungkin
    // melihat atau menandai notifikasi milik pengguna lain.

    public function index()
    {
        $notifikasis = Auth::user()->notifications()->latest()->paginate(15);

        return view('notifikasi.index', compact('notifikasis'));
    }

    public function baca(string $id)
    {
        // findOrFail lewat relasi: id milik orang lain -> 404
        $notifikasi = Auth::user()->notifications()->findOrFail($id);
        $notifikasi->markAsRead();

        return back();
    }

    public function bacaSemua()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back();
    }
}