<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawans = User::orderByDesc('is_active')->orderBy('name')->get();

        return view('karyawan.index', compact('karyawans'));
    }

    public function toggleStatus(Request $request, User $user)
    {
        // Admin tidak boleh menonaktifkan akunnya sendiri
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        // Diset langsung (bukan mass assignment) agar is_active tidak perlu masuk $fillable
        $user->is_active = ! $user->is_active;
        $user->save();

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', 'Akun ' . $user->name . ' berhasil ' . $status . '.');
    }
}
