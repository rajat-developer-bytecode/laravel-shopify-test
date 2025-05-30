<?php

namespace App\Helpers;
use GuzzleHttp\Client;

class ShopifyRequestHelper {
    public static function makeAnAPICallToShopify($method, $endpoint, $headers, $url_params = null, $requestBody = null) {
        //Headers
        /**
         * Content-Type: application/json
         * X-Shopify-Access-Token: value
         */
        //Log::info('Endpoint '.$endpoint);
        try {
            $client = new Client();
            $response = null;
            if($method == 'GET' || $method == 'DELETE') {
                $response = $client->request($method, $endpoint, [ 'headers' => $headers ]);
            } else {
                $response = $client->request($method, $endpoint, [ 'headers' => $headers, 'json' => $requestBody ]);
            }
            return [
                'statusCode' => $response->getStatusCode(),
                'body' => json_decode($response->getBody(), true)
            ];
        } catch(\Exception $e) {
            return [
                'statusCode' => $e->getCode(),
                'message' => $e->getMessage(),
                'body' => null
            ];
        }
    }

    public static function getShopifyURLForStore($endpoint) {
        return env('SHOPIFY_APP_MAIN_URL').'/admin/api/'.env('SHOPIFY_APP_API_VERSION').'/'.$endpoint;
    }

    public static function getShopifyHeadersForStore($method = 'GET') {
        return $method == 'GET' ? [
            'Content-Type' => 'application/json',
            'X-Shopify-Access-Token' => env('SHOPIFY_APP_ACCESS_TOKEN')
        ] : [
            'Content-Type: application/json',
            'X-Shopify-Access-Token: '.env('SHOPIFY_APP_ACCESS_TOKEN')
        ];
    }
}
