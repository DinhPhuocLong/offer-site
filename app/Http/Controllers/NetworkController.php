<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\Network;
use Illuminate\Http\Request;

class NetworkController extends Controller
{
    public function index()
    {
//        return response()->json(['data' => config('constants.OFFER_TYPE_OFFER') ]);
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
            'is_hidden' => 'nullable',
            'is_daily_click_reset' => 'nullable'
        ]);
        $data = Network::create($validated);
        return response($data);
    }

    public function update(Request $request) {
        $network = Network::find($request->query('id'));
        $request->validate([
            'name' => 'required|string',
            'aff_sub' => 'required|string',
            'payout' => 'string',
        ]);
        if($network) {
            $network->update([
                'name' => $request->name,
                'aff_sub' => $request->aff_sub,
                'payout' => $request->payout,
                'is_unique_click' => $request->is_unique_click,
                'is_unique_lead' => $request->is_unique_lead,
                'is_hidden' => $request->is_hidden,
                'is_daily_click_reset' => $request->is_daily_click_reset
            ]);
            return response()->json(['msg' => 'successfully']);
        }
        return response()->json(['msg' => 'Failed, can not find such network'], 404);
    }
    public function delete(Request $request) {
        $network = Network::find($request->id);
        if($network) {
            $network->delete();
            return response()->json(['msg' => 'successfully'], 204);
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
