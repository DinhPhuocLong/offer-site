<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use Illuminate\Http\Request;

class DomainController extends Controller
{
    public function index() {
        $data = Domain::all();
        return response()->json([
            'data' => $data,
        ]);
    }
}
