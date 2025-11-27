<?php

namespace App\Http\Controllers\Almoxarife;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function profile()
    {
        return view('almoxarife.profile');
    }
}
