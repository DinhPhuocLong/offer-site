<?php

namespace App\Http\Controllers;

use App\Models\Click;
use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;

class ClickController extends Controller
{
    public function index() {
        $data = Click::all();
        return response()->json(['data' => $data]);
    }
    public function create(Request $request) {
        $ip = $request->ip();
        if ($position = Location::get($ip)) {
            // Successfully retrieved position.
            return $position->countryName;
        } else {
            return 'Failed to ';
        }
    }
}
