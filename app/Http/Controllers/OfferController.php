<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page');
        if (!$perPage) $perPage = 5;
        $data = Offer::with('network:id,name')->with('domain:id,domain_url')->orderBy('created_at','DESC')->paginate($perPage);
        $filteredData = $data->map(function ($offer) {
            return [
                'id' => $offer->id,
                'network_id' => $offer->network_id,
                'offer_name' => $offer->offer_name,
                'offer_type' => $offer->offer_type,
                'offer_link' => $offer->offer_link,
                'country_allowed' => $offer->country_allowed,
                'offer_payout' => $offer->offer_payout,
                'is_hidden' => $offer->is_hidden,
                'network_name' => $offer->network->name,
                'offer_domain_url' => $offer->domain->domain_url,
                'offer_lead_link' => $offer->domain->domain_url . '/click/l?offer=' . $offer->id . '&pub=' . auth()->user()->id,
                'offer_click_link' => $offer->domain->domain_url . '/click?offer=' . $offer->id . '&pub=' . auth()->user()->id,
                'created_at' => $offer->created_at,
                'updated_at' => $offer->updated_at,
            ];
        });
        return response()->json(
            [
                'data' => $filteredData,
                'pagination' => [
                    'first_page_number' => 1,
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'next_page_number' => $data->currentPage() < $data->lastPage() ? $data->currentPage() + 1 : null,
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'from' => $data->firstItem(),
                    'to' => $data->lastItem(),
                ]
            ]);
    }

    public function create(Request $request)
    {
        $validated = $request->validate([
            'offer_name' => 'required|max:255|unique:offers',
            'network_id' => 'required|max:20',
            'offer_domain' => 'required|max:20',
            'offer_type' => 'nullable',
            'offer_link' => 'required|max:400',
            'country_allowed' => 'nullable',
            'offer_payout' => 'nullable',
            'is_hidden' => 'nullable'
        ]);
        $data = Offer::create($validated);
        return response($data);
    }

    public function show($id)
    {
        $data = Offer::find($id);
        if (!$data) return response()->json([
            'msg' => 'Offer not found'
        ], 404);
        return  response()->json([
            'data' => $data
        ]);
    }
    public function update(Request $request, $id) {
        $offer = Offer::find($id);
        $validated = $request->validate([
            'offer_name' => [
                'required',
                'max:255',
                Rule::unique('offers')->ignore($offer->id),
            ],
            'network_id' => 'required|max:20',
            'offer_domain' => 'required|max:20',
            'offer_type' => 'nullable',
            'offer_link' => 'required|max:400',
            'country_allowed' => 'nullable',
            'offer_payout' => 'nullable',
            'is_hidden' => 'nullable'
        ]);
        if ($offer) {
            $offer->update($request->all());
            return response()->json(['msg' => 'successfully']);
        }
        return response()->json(['msg' => 'Failed, can not find such offer'], 404);
    }
    public function delete(Request $request) {
        $offer = Offer::find($request->id);
        if($offer) {
            $offer->delete();
            return response()->json(['msg' => 'successfully'], 204);
        }
        return response()->json(['msg' => 'Failed, can not find such offer'], 404);
    }
}


