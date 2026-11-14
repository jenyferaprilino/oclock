<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil customer.
     */
    public function show(Request $request): View
    {
        return view('customer.profile', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Perbarui data profil customer (nama & telepon).
     * Email dikunci demi konsistensi dan integritas akun.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->update([
            'name' => $request->validated('name'),
            'phone' => $request->validated('phone'),
        ]);

        return redirect()->route('customer.profile')
            ->with('success', 'Profil Anda berhasil diperbarui.');
    }
}
