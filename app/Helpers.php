<?php

use Illuminate\Support\Facades\Http;

function createPremiumAccess($data)
{
    $url = env('API_URL') . '/api/my-courses/premium';
    try {
        $response = Http::post($url, $data);
        $data = $response->json();
        $data['http_code'] = $response->getStatusCode();
        return $data;
    } catch (\Throwable $th) {
        return $th->getMessage();
    }
}


function postOrder($params)
{
    $url = env('API_URL') . '/api/order';
    try {
        $response = Http::post($url, $params);
        $data = $response->json();
        $data['http_code'] = $response->getStatusCode();
        return $data;
    } catch (\Throwable $th) {
        return $th->getMessage();
    }
}
