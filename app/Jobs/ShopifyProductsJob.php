<?php

namespace App\Jobs;

use App\Helpers\ShopifyRequestHelper;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ShopifyProductsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $getStoreRequest = 'products(first: 10) {
                                    edges {
                                        node {
                                            id
                                            descriptionHtml
                                            status
                                            title
                                            updatedAt
                                            vendor
                                            defaultCursor
                                            handle
                                            productType
                                            publishedAt
                                            variantsCount {
                                                count
                                                precision
                                            }
                                            createdAt
                                            media(first: 10) {
                                                edges {
                                                    node {
                                                        id
                                                        preview {
                                                            image {
                                                                height
                                                                url
                                                                width
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    cursor
                                    }
                                }';
            $payload = ['query' => $getStoreRequest];
            $endpoint = ShopifyRequestHelper::getShopifyURLForStore('graphql.json');
            $headers = ShopifyRequestHelper::getShopifyHeadersForStore('GET');
            $response = ShopifyRequestHelper::makeAnAPICallToShopify('POST', $endpoint, $headers, null, $payload);
            dd($response);
            if($response['statusCode'] != 200){
                die("Something went wrong.");
            } else {
                $responseData = (!empty($response['body']) && !empty($response['body']['data']) && !empty($response['body']['data']['shop'])) ? $response['body']['data']['shop'] : [];
                $storeID = !empty($responseData['id']) ? explode("/", $responseData['id']) : "";
                if(!empty($storeID)){
                    $data = [
                        'name' => $responseData['name'] ?? "",
                        'email' => $responseData['email'] ?? "",
                        'access_token' => env('SHOPIFY_APP_ACCESS_TOKEN'),
                        'shop_owner_name' => $responseData['shopOwnerName'] ?? "",
                        'myshopify_domain' => $responseData['url'] ?? "",
                    ];
                    Product::updateOrCreate(['store_id' => end($storeID)],$data);
                    die("Store updated successfully.");
                }
            }
        } catch (\Exception $e) {
            Log::error("Status Code: " . $e->getCode()." message: ". $e->getMessage() . " at line: " . $e->getLine());
            die("Exception: " . $e->getMessage());
        }
    }
}
