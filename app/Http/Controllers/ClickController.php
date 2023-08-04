<?php

namespace App\Http\Controllers;

use App\Events\NewNotify;
use App\Models\Click;
use App\Models\Network;
use App\Models\Offer;
use App\Models\PostbackError;
use App\Models\User;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;
use Ramsey\Uuid\Uuid;
use Stevebauman\Location\Facades\Location;
use Illuminate\Support\Facades\Broadcast;


class ClickController extends Controller
{
    public function index() {
        $data = Click::clickBelongTo()->get();
        return response()->json(['data' => $data]);
    }
    public function create(Request $request) {
        $ip = $request->ip();
        $agent = new Agent();
        $offerId = $request->query('offer');
        $pubId = $request->query('pub');
        if(!$pubId) {
            $pubId = 1;
        }
        $pub = User::withoutGlobalScopes()->find($pubId);
        $offer = Offer::withoutGlobalScopes()->find($offerId);
        if(!$offer) {
            return "Invalid offer";
        }
        $network = Network::withoutGlobalScopes()->find($offer->network_id);

        // check if user country is allowed
        $country = $this->getClientCountry($ip);
        $country = $this->formatCountryName($country);
        $allowed_country = $this->getListAllowedCountry($offer->country_allowed);
        if (!in_array($country, $allowed_country)) {
            return "Failed, this offer is not working with {$country}";
        }

        //store click to database and get uuid to attach to url
        $platform = $agent->platform();
        $platformAndVersion = $platform."-{$agent->version($platform)}";
        $rawUserAgent = $agent->getUserAgent();
        $clickId = Click::create([
            'ip' => $ip,
            'offer_id' => $offerId,
            'user_id' => $pub->id,
            'country' => $country,
            'browser' => $agent->browser(),
            'os' => $platformAndVersion,
            'user_agent' => $rawUserAgent,
            'uuid' => Uuid::uuid4()->toString()
        ]);

        // get rid of parameter bracket and then redirect to offer link with parameter
        $aff_sub = trim($network->aff_sub, '{}, []');
        $redirectLink = $offer->offer_link."?{$aff_sub}={$clickId->uuid}";
        return redirect($redirectLink);
    }
    public function createLeadClick(Request $request) {
        $ip = $request->ip();
        $agent = new Agent();
        $offerId = $request->query('offer');
        $pubId = $request->query('pub');
        if(!$pubId) {
            $pubId = 1;
        }
        $pub = User::withoutGlobalScopes()->find($pubId);
        $offer = Offer::withoutGlobalScopes()->find($offerId);
        if(!$offer) {
            return "Invalid offer";
        }
        $network = Network::withoutGlobalScopes()->find($offer->network_id);

        // check click unique if network click is unique
        if ($network->is_unique_click) {
            if($this->checkUniqueClickIp($offerId, $ip)) {
                return "Error, This click to lead IP is already exist";
            }
        }
        //check lead unique if network lead is unique
        if ($network->is_unique_lead) {
            if($this->checkUniqueConversionIp($offerId, $ip)) {
                return "Error, This IP is already converted";
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
        $platform = $agent->platform();
        $platformAndVersion = $platform."-{$agent->version($platform)}";
        $rawUserAgent = $agent->getUserAgent();
        $clickId = Click::create([
            'ip' => $ip,
            'offer_id' => $offerId,
            'user_id' => $pub->id,
            'country' => $country,
            'browser' => $agent->browser(),
            'os' => $platformAndVersion,
            'user_agent' => $rawUserAgent,
            'uuid' => Uuid::uuid4()->toString(),
            'is_click_lead' => 1
        ]);

        // get rid of parameter bracket and then redirect to offer link with parameter
        $aff_sub = trim($network->aff_sub, '{}, []');
        $redirectLink = $offer->offer_link."?{$aff_sub}={$clickId->uuid}";
        return redirect($redirectLink);
    }

    public function createConversion(Request $request) {
        $cid = $request->query('cid');
        $payout = $request->query('payout');
        if (!$payout) {
            $payout = 1;
        }

        //check if there is cid parameter
        if(!$cid) {
            return response()->json([
                'msg' => 'Invalid click id'
            ], 400);
        }

        $clickId = Click::where([
            ['is_click_lead', 1],
            ['uuid', $cid]
        ])->first();
        // check again if click id exist in database
        $ip = $request->ip();

        //write to postback error if invalid
        if (!$clickId) {
            PostbackError::create([
                'cid' => $cid,
                'sender_ip' => $ip,
                'payout' => $payout
            ]);
            return response()->json([
                'msg' => 'Invalid click id'
            ], 400);
        }

        // Check if offer has a default payout, if not then user payout from network
        $revenue = Offer::withoutGlobalScopes()->find($clickId->offer_id)->offer_payout;
        if (!$revenue) {
            $revenue = $payout;
        }
        $result = $clickId->update([
            'is_converted' => 1,
            'offer_payout' => $revenue
        ]);
        if (!$result) {
            return response()->json([
                'msg' => 'Error'
            ], 500);
        }

        //Broadcast to client

        event(new NewNotify([
            'Click' => $clickId
        ]));

        return response()->json([
            'msg' => 'Conversion created successfully'
        ], 200);
    }

    public function getConversions() {

    }

    public function getClientCountry($ip) {
        if ($position = Location::get($ip)) {
            // Successfully retrieved location.
            return $position->countryName;
        } else {
            // Failed to read ip's location
            return "Failed to read ip location";
        }
    }

    public function formatCountryName($country) {
        $countries = array(
            'Afghanistan' => 'AF',
            'Albania' => 'AL',
            'Algeria' => 'DZ',
            'American Samoa' => 'AS',
            'Andorra' => 'AD',
            'Angola' => 'AO',
            'Anguilla' => 'AI',
            'Antarctica' => 'AQ',
            'Antigua and Barbuda' => 'AG',
            'Argentina' => 'AR',
            'Armenia' => 'AM',
            'Aruba' => 'AW',
            'Australia' => 'AU',
            'Austria' => 'AT',
            'Azerbaijan' => 'AZ',
            'Bahamas' => 'BS',
            'Bahrain' => 'BH',
            'Bangladesh' => 'BD',
            'Barbados' => 'BB',
            'Belarus' => 'BY',
            'Belgium' => 'BE',
            'Belize' => 'BZ',
            'Benin' => 'BJ',
            'Bermuda' => 'BM',
            'Bhutan' => 'BT',
            'Bolivia' => 'BO',
            'Bosnia and Herzegovina' => 'BA',
            'Botswana' => 'BW',
            'Bouvet Island' => 'BV',
            'Brazil' => 'BR',
            'British Indian Ocean Territory' => 'IO',
            'Brunei Darussalam' => 'BN',
            'Bulgaria' => 'BG',
            'Burkina Faso' => 'BF',
            'Burundi' => 'BI',
            'Cambodia' => 'KH',
            'Cameroon' => 'CM',
            'Canada' => 'CA',
            'Cape Verde' => 'CV',
            'Cayman Islands' => 'KY',
            'Central African Republic' => 'CF',
            'Chad' => 'TD',
            'Chile' => 'CL',
            'China' => 'CN',
            'Christmas Island' => 'CX',
            'Cocos (Keeling) Islands' => 'CC',
            'Colombia' => 'CO',
            'Comoros' => 'KM',
            'Congo' => 'CG',
            'Democratic Republic of the Congo' => 'CD',
            'Cook Islands' => 'CK',
            'Costa Rica' => 'CR',
            'Croatia' => 'HR',
            'Cuba' => 'CU',
            'Cyprus' => 'CY',
            'Czech Republic' => 'CZ',
            'Denmark' => 'DK',
            'Djibouti' => 'DJ',
            'Dominica' => 'DM',
            'Dominican Republic' => 'DO',
            'Ecuador' => 'EC',
            'Egypt' => 'EG',
            'El Salvador' => 'SV',
            'Equatorial Guinea' => 'GQ',
            'Eritrea' => 'ER',
            'Estonia' => 'EE',
            'Ethiopia' => 'ET',
            'Falkland Islands (Malvinas)' => 'FK',
            'Faroe Islands' => 'FO',
            'Fiji' => 'FJ',
            'Finland' => 'FI',
            'France' => 'FR',
            'French Guiana' => 'GF',
            'French Polynesia' => 'PF',
            'French Southern Territories' => 'TF',
            'Gabon' => 'GA',
            'Gambia' => 'GM',
            'Georgia' => 'GE',
            'Germany' => 'DE',
            'Ghana' => 'GH',
            'Gibraltar' => 'GI',
            'Greece' => 'GR',
            'Greenland' => 'GL',
            'Grenada' => 'GD',
            'Guadeloupe' => 'GP',
            'Guam' => 'GU',
            'Guatemala' => 'GT',
            'Guernsey' => 'GG',
            'Guinea' => 'GN',
            'Guinea-Bissau' => 'GW',
            'Guyana' => 'GY',
            'Haiti' => 'HT',
            'Heard Island and McDonald Islands' => 'HM',
            'Holy See (Vatican City State)' => 'VA',
            'Honduras' => 'HN',
            'Hong Kong' => 'HK',
            'Hungary' => 'HU',
            'Iceland' => 'IS',
            'India' => 'IN',
            'Indonesia' => 'ID',
            'Iran, Islamic Republic of' => 'IR',
            'Iraq' => 'IQ',
            'Ireland' => 'IE',
            'Deutschland' => 'DE',
            'United States' => 'US',
            'United Kingdom' => 'GB',
            'Italy' => 'IT',
            'Sweden' => 'SE',
            'Switzerland' => 'CH',
            'Spain' => 'ES',
            'Portugal' => 'PT',
            'Poland' => 'PL',
            'Russia' => 'RU',
            'New Zealand' => 'NZ',
            'Singapore' => 'SG',
            'Netherlands' => 'NL',
            'Vietnam' => 'VN',
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
        return Click::where([
            ['offer_id', $offerId],
            ['is_click_lead', 1],
            ['ip', $ip]
        ])->exists();
    }

    public function checkUniqueConversionIp($offerId, $ip) {
        return Click::where([
            ['offer_id', $offerId],
            ['is_click_lead', 1],
            ['is_converted', 1],
            ['ip', $ip]
        ])->exists();
    }

}
