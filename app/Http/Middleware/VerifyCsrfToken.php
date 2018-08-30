<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/parcels/parcel-lookup',
        '/poc/tfa/getsms',
        '/poc/tfa/getsms/failed',
        '/poc/tfa/getvoice',
        '/poc/tfa/getvoice/failed',
        '/poc/tfa/generateFaxPdf'
    ];
}
