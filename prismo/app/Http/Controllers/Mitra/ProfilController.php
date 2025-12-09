<?php

namespace App\Http\Controllers\Mitra;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $mitraProfile = $user->mitraProfile;
        
        return view('mitra.profil.profil', compact('user', 'mitraProfile'));
    }
}
