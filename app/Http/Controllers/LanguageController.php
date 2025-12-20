<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    public function switch($locale)
    {
        if (! in_array($locale, ['en', 'kh'])) {
            abort(400);
        }

        session(['app_locale' => $locale]);

        return back();
    }
}
