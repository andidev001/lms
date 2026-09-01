<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SekolahController extends Controller
{
    public function index()
    {
        $sekolah = Sekolah::first();
        
        // If somehow deleted, recreate it
        if(!$sekolah) {
            $sekolah = Sekolah::create([
                'nama_sekolah' => 'LMS Sekolahku'
            ]);
        }

        return view('admin.sekolahs.index', compact('sekolah'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'nama_sekolah' => 'required|string|max:255',
            'npsn' => 'nullable|string|max:50',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'kepala_sekolah' => 'nullable|string|max:255',
            'nip_kepala_sekolah' => 'nullable|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $sekolah = Sekolah::first();

        $data = $request->except(['_token', '_method', 'logo']);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($sekolah->logo && Storage::disk('public')->exists($sekolah->logo)) {
                Storage::disk('public')->delete($sekolah->logo);
            }
            
            $path = $request->file('logo')->store('logos', 'public');
            $data['logo'] = $path;
        }

        $sekolah->update($data);

        return redirect()->route('sekolah.index')
                        ->with('success', 'Profil Sekolah berhasil diperbarui.');
    }
}
