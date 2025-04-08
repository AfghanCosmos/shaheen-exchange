<?php

namespace App\Http\Controllers;

use App\Models\Hawla;
use Illuminate\Http\Request;

class HawlaPrintController extends Controller
{
    public function print(Hawla $hawla)
    {
        return view('hawla.print', compact('hawla'));
    }
}
