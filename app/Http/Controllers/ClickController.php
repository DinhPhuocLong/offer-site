<?php

namespace App\Http\Controllers;

use App\Models\Click;
use Illuminate\Http\Request;

class ClickController extends Controller
{
    public function index() {
        $data = Click::all();
        return response()->json(['data' => $data]);
    }
    public function create(Request $request) {
        $ip = $request->ip();
    }
}
