<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthorizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function suPanel()
    {
        $this->authorize('sudata');

        return 'SUPER ADMIN';
    }

    public function admPanel()
    {
        $this->authorize('admdata');

        return 'ADMIN';
    }

    public function csPanel()
    {
        $this->authorize('csdata');

        return 'CS';
    }
}
