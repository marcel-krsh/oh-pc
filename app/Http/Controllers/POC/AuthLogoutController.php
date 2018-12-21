<?php

namespace App\Http\Controllers\POC;

use App\Http\Controllers\Controller;

class AuthLogoutController extends Controller
{

    public function destroy()
    {
        // @todo: log out the user in the real way

        \Request::session()->flush();
        \Cookie::forget();
    }
}
