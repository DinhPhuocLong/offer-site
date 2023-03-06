<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index() {
        $data = Offer::all();
        return response()->json(['data' => $data]);
    }
    public function create(Request $request) {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'network_id' => 'required|max:20',
            'offer_type' => 'nullable',
            'offer_link' => 'required|max:400',
            'country_allowed' => 'nullable',
            'offer_payout' => 'nullable',
            'is_hidden' => 'nullable'
        ]);
        $data = Offer::create($validated);
        return response($data);
    }
}
