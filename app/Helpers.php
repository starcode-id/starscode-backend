<?php

use Illuminate\Support\Facades\Http;

function getUser($userId)
{
    $url = env('APP_URL') . '/api/users/' . $userId;
    try {
        $response = Http::timeout(10)->get($url);
        $data = $response->json();
        $data['http_code'] = $response->getStatusCode();
        return $data;
    } catch (\Throwable $th) {
        return [
            'status' => false,
            'http_code' => 500,
            'message' => $th->getMessage()
        ];
    }
}
function getUserById($userId = [])
{
    $url = env('APP_URL') . '/api/users/' . $userId;
    try {
        if (count($userId) === 0) {
            return [
                'status' => false,
                'http_code' => 200,
                'data' => [],
            ];
        }
        $response = Http::timeout(10)->get($url, ['user_id' => $userId]);
        $data = $response->json();
        $data['http_code'] = $response->getStatusCode();
        return $data;
    } catch (\Throwable $th) {
        return [
            'status' => false,
            'http_code' => 500,
            'message' => $th->getMessage()
        ];
    }
}
