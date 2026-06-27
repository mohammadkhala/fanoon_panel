<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class HelpController extends Controller
{
    public function index(): View
    {
        return view('admin-views.help.index');
    }
}
