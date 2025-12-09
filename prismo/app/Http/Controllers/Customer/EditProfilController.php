<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EditProfilController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('customer.profil.eprofil', compact('user'));
    }
}
