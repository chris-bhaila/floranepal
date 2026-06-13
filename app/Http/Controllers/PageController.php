<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function showSubPage($name)
    {
        if (!preg_match('/^[a-zA-Z0-9.\-]+$/', $name) || str_contains($name, '..')) {
            abort(404);
        }

        if (view()->exists("pages.dashboard.$name")) {
            return view("pages.dashboard.$name");
        }

        abort(404);
    }
}
