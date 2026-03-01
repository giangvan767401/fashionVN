<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function mission()
    {
        return view('pages.mission');
    }

    public function sustainability()
    {
        return view('pages.sustainability');
    }

    public function faq()
    {
        return view('pages.faq');
    }

    public function contact()
    {
        return view('pages.contact');
    }
}
