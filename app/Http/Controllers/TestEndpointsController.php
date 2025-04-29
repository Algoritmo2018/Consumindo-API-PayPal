<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class TestEndpointsController extends Controller
{
    public function get_access_token()
    {

        $url = 'https://api-m.sandbox.paypal.com/v1/oauth2/token';
        $clientId = "ASoP2k04iYEi_16P8AFOZcudNKhF-hxsWDVRFlD4mW2ycjq33Am_qib7IhKko8uJL3l7fNlsLPf5wZym";
        $clientSecret = "EOT1BDcqv5q8nVYvldch0NjAwJRkUx7g7Ct6jfWTbaCU_xG_NseWcgrUiMHDCPKd9TCDujBilMuywd-r";

        $response = Http::withBasicAuth($clientId, $clientSecret)
            ->withHeaders([
                "Content-Type" => "application/x-www-form-urlencoded"
            ])->asForm()->post($url, ['grant_type' => 'client_credentials']);

        DB::table('tokens')
            ->updateOrInsert(
                ['id' => 1],
                ['token' => $response->json()['access_token']]
            );

        return $response->json();
    }
    public function createOrder()
    {
        $url = "https://api-m.sandbox.paypal.com/v2/checkout/orders";

        $response = Http::withHeaders([

            'Content-Type' => 'application/json',
            'PayPal-Request-Id' => '7b92603e-77ed-4896-8e78-5dea2050476a',
            'Authorization' => 'Bearer ' . Token::where('id', 1)->pluck('token')[0]
        ])->post($url, [
            "intent" => "CAPTURE",
            "payment_source" => [
                "paypal" => [
                    "experience_context" => [
                        "payment_method_preference" => "IMMEDIATE_PAYMENT_REQUIRED",
                        "landing_page" => "LOGIN",
                        "shipping_preference" => "GET_FROM_FILE",
                        "user_action" => "PAY_NOW",
                        "return_url" => "https://example.com/returnUrl",
                        "cancel_url" => "https://example.com/cancelUrl"
                    ]
                ]
            ],
            "purchase_units" => [
                [
                    "invoice_id" => "90210",
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => "230.00",
                        "breakdown" => [
                            "item_total" => [
                                "currency_code" => "USD",
                                "value" => "220.00"
                            ],
                            "shipping" => [
                                "currency_code" => "USD",
                                "value" => "10.00"
                            ]
                        ]
                    ],
                    "items" => [
                        [
                            "name" => "T-Shirt",
                            "description" => "Super Fresh Shirt",
                            "unit_amount" => [
                                "currency_code" => "USD",
                                "value" => "20.00"
                            ],
                            "quantity" => "1",
                            "category" => "PHYSICAL_GOODS",
                            "sku" => "sku01",
                            "image_url" => "https://example.com/static/images/items/1/tshirt_green.jpg",
                            "url" => "https://example.com/url-to-the-item-being-purchased-1",
                            "upc" => [
                                "type" => "UPC-A",
                                "code" => "123456789012"
                            ]
                        ],
                        [
                            "name" => "Shoes",
                            "description" => "Running, Size 10.5",
                            "sku" => "sku02",
                            "unit_amount" => [
                                "currency_code" => "USD",
                                "value" => "100.00"
                            ],
                            "quantity" => "2",
                            "category" => "PHYSICAL_GOODS",
                            "image_url" => "https://example.com/static/images/items/1/shoes_running.jpg",
                            "url" => "https://example.com/url-to-the-item-being-purchased-2",
                            "upc" => [
                                "type" => "UPC-A",
                                "code" => "987654321012"
                            ]
                        ]
                    ]
                ]
            ]
        ]);
        return $response->json();
    }

    public function order_detail(?string $id){
        $response=Http::withHeaders([ 
            'Authorization' => 'Bearer ' . Token::where('id', 1)->pluck('token')[0]
        ])->withUrlParameters([
            'endpoint' => 'https://api-m.sandbox.paypal.com/v2/checkout/orders',
            'id' => $id, 
        ])->get('{+endpoint}/{id}');

        return $response->json();
    }
}
