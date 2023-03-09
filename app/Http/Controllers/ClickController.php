<?php

namespace App\Http\Controllers;

use App\Models\Click;
use App\Models\Network;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;
use Ramsey\Uuid\Uuid;
use Stevebauman\Location\Facades\Location;

class ClickController extends Controller
{
    public function index() {
        $data = Click::all();
        return response()->json(['data' => $data]);
    }
    public function create(Request $request) {
        $ip = $request->ip();
        $agent = new Agent();
        $offerId = $request->query('offer');
        $pubId = $request->query('pub');
        if(!$pubId) {
            $pubId = 0;
        }
        $pub = User::find($pubId);
        $offer = Offer::find($offerId);
        if(!$offer) {
            return "Invalid offer";
        }
        $network = Network::find($offer->network_id);

        // check click unique if network click is unique
        if ($network->is_unique_click) {
            if($this->checkUniqueClickIp($offerId, $ip)) {
                return "Error, This click IP is already exsit";
            }
        }

        // check if user country is allowed
        $country = $this->getClientCountry($ip);
        $country = $this->formatCountryName($country);
        $allowed_country = $this->getListAllowedCountry($offer->country_allowed);
        if (!in_array($country, $allowed_country)) {
            return "Failed, this offer is not working with {$country}";
        }

        //store click to database and get uuid to attach to url
        $clickId = Click::create([
            'ip' => $ip,
            'offer_id' => $offerId,
            'user_id' => $pub->id,
            'country' => $country,
            'browser' => $agent->browser(),
            'os' => $agent->platform(),
            'uuid' => Uuid::uuid4()->toString()
        ]);

        // get rid of parameter bracket and then redirect to offer link with parameter
        $aff_sub = trim($network->aff_sub, '{}, []');
        $redirectLink = $offer->offer_link."?{$aff_sub}={$clickId->uuid}";
        return redirect($redirectLink);
    }
    public function getClientCountry($ip) {
        if ($position = Location::get($ip)) {
            // Successfully retrieved position.
            return $position->countryName;
        } else {
            // Failed to read ip's location
            return "Failed to read ip location";
        }
    }
    public function formatCountryName($country) {
        $countries = array(
            'Vietnam' => 'VN',
            'United States of America' => 'USA',
            'United Kingdom' => 'UK',
        );
        if (!strlen(trim($country))) {
            return "False";
        }
        if (array_key_exists($country, $countries)) {
            return $countries[$country];
        } else {
            return "False"; // or any default value you prefer
        }
    }

    public function getListAllowedCountry($list) {
        $allowed_country = explode(',', $list);
        foreach ($allowed_country as &$value) {
            $value = trim($value);
            $value = strtoupper($value);
        }
        return $allowed_country;
    }
    public function checkUniqueClickIp($offerId, $ip) {
        $clickOfCurrentOffer = Click::where('offer_id', $offerId)->get();
        $isRecordedIp =  $clickOfCurrentOffer->where('ip', $ip)->first();
        if ($isRecordedIp) {
            return "This click ip is duplicated";
        }
    }

}
