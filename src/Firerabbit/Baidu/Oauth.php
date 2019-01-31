<?php
namespace Firerabbit\Baidu;

use GuzzleHttp\Client;

class Oauth
{
    const AUTHORITE_CODE_URL = 'http://openapi.baidu.com/oauth/2.0/authorize';
    const AUTHORITE_TOKEN_URL = 'https://openapi.baidu.com/oauth/2.0/token';
    const USER_INFO_URL = 'https://openapi.baidu.com/rest/2.0/passport/users/getInfo';

    public static function getAuthoriteCodeUrl()
    {
        $params = [
            'client_id' => config('services.baidu.client_id'),
            'response_type' => 'code',
            'redirect_uri' => config('services.baidu.redirect'),
        ];

        return self::AUTHORITE_CODE_URL . '?' . http_build_query($params);
    }

    public static function getAccessToken($code)
    {
        $params = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => config('services.baidu.client_id'),
            'client_secret' => config('services.baidu.client_secret'),
            'redirect_uri' => config('services.baidu.redirect'),
        ];

        $response = self::post(self::AUTHORITE_TOKEN_URL, $params);

        return $response;
    }

    public static function getUserInfo($accessToken = '')
    {
        $params = ['access_token' => $accessToken];
        $response = self::post(self::USER_INFO_URL, $params);

        return $response;
    }

    private static function post($url, $params)
    {
        $client = new Client();
        $response = $client->post($url, [
            'query' => $params,
            'verify' => false,
        ]);

        $toArray = json_decode($response->getBody()->getContents(), true);

        return $toArray;
    }
}