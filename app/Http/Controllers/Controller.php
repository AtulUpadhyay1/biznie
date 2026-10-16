<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Trim and upper-case identifier inputs (GSTIN, PAN, IFSC) before they are
     * validated and stored, so "27aapfu0939f1zv " and "27AAPFU0939F1ZV" are
     * the same value everywhere.
     */
    protected function uppercaseInputs(Request $request, string ...$keys): void
    {
        $normalised = [];
        foreach ($keys as $key) {
            if (is_string($request->input($key))) {
                $normalised[$key] = strtoupper(trim($request->input($key)));
            }
        }
        $request->merge($normalised);
    }
}
