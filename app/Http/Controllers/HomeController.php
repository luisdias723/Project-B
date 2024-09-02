<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    // Ponto de entrada para a aplicação

    public function index()
    {
        return view('home');
    }
}
