<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Network;
use Illuminate\Http\Request;

class NetworkController extends Controller
{
    public function index()
    {
        $data = Network::all();
        return response()->json(['data' => $data]);
    }
    public function create(Request $request) {
        $validated = $request->validate([
            'name' => 'required|unique:networks|max:255',
            'aff_sub' => 'required|max:20',
            'payout' => 'required|max:20',
            'is_unique_click' => 'nullable',
            'is_unique_lead' => 'nullable',
            'is_hidden' => 'nullable'
        ]);
        $data = Network::create($validated);
        return response($data);
    }
    public function hide(Request $request) {
        $network = Network::find($request->id);
        if($network) {
            $network->update(['is_hidden' => 1]);
            return response()->json(['msg' => 'successfully']);
        }
        return response()->json(['msg' => 'Failed, can not find such network'], 404);
    }
    public function delete(Request $request) {
        $network = Network::find($request->id);
        if($network) {
            $network->delete();
            return response()->json(['msg' => 'successfully']);
        }
        return response()->json(['msg' => 'Failed, can not find such network'], 404);
    }
    public function showNetworkPostbackUrl(Request $request) {
        $networkId = $request->query('id');
        $network = Network::find($networkId);
        $domains = Domain::where('is_hidden', false)->get();
        if($network) {
            $postbaclUrls = array_map(function ($item) use ($network) {
                return $item['domain_url'] . '?cid=' . $network->aff_sub . '&payout=' . $network->payout;
            }, $domains->toArray());
            return response()->json($postbaclUrls);
        }
        return response()->json(['msg' => 'Failed, can not find such network'], 404);
    }
}
