<?php

use App\Jobs\ShopifyStoreJob;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/store', function () {
    $jobId = ShopifyStoreJob::dispatch();
});
