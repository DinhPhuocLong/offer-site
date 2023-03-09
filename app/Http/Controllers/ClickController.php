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
        $offerId = $request->query('offer');
        $pubId = 0;
        if($request->query('pub')) {
            $pubId = $request->query('pub');
        }
        $pub = User::find($pubId);
        $offer = Offer::find($offerId);
        $network = Network::find($offer->network_id);
        $ip = $request->ip();
        $country = $this->getClientCountry($ip);
        $country = $this->formatCountryName($country);
        $allowed_country = $this->getListAllowedCountry($offer->country_allowed);
        if (!in_array($country, $allowed_country)) {
            return "Failed, this offer is not working with {$country}";
        }
        return $network;
        $clickId = Click::create([
            'ip' => $ip,
            'offer_id' => $offerId,
            'user_id' => $pubId,
            'country' => 'Vietnam',
            'browser' => 'Chrome',
            'os' => 'window10',
            'uuid' => Uuid::uuid4()->toString()
        ]);
        return $clickId->uuid;
    }
    public function getClientCountry($ip) {
        if ($position = Location::get($ip)) {
            // Successfully retrieved position.
            return $position->countryName;
        } else {
            // Failed to read ip's location
            return false;
        }
    }
    public function formatCountryName($country) {
        $countries = array(
            'Vietnam' => 'VN',
            'United States of America' => 'USA',
            'United Kingdom' => 'UK',
            // ...
        );
        return $abbreviation = array_search($country, $countries);
    }

    public function getListAllowedCountry($list) {
        $allowed_country = explode(',', $list);
        foreach ($allowed_country as &$value) {
            $value = trim($value);
            $value = strtoupper($value);
        }
        return $allowed_country;
    }
}
