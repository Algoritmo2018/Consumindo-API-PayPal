<?php

namespace App\Http\Controllers\Authorization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TestEndpointsController extends Controller
{
    public function get_access_token()
    {
        $url = 'https://api-m.sandbox.paypal.com/v1/oauth2/token';
        $clientId = "ASoP2k04iYEi_16P8AFOZcudNKhF-hxsWDVRFlD4mW2ycjq33Am_qib7IhKko8uJL3l7fNlsLPf5wZym";
        $clientSecret = "EOT1BDcqv5q8nVYvldch0NjAwJRkUx7g7Ct6jfWTbaCU_xG_NseWcgrUiMHDCPKd9TCDujBilMuywd-r";

        $response = Http::withBasicAuth($clientId, $clientSecret)
        ->withHeaders([ "Content-Type" => "application/x-www-form-urlencoded"
        ])->asForm()->post($url, ['grant_type' => 'client_credentials']);
        dd($response->json() );
    }
}
