<?php

namespace Roman\WildberriesApi;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\TransferException;

class Wildberries
{
    private const API_URI = 'https://suppliers-api.wildberries.ru';
    private const TEST_API_URI = 'http://wb-api.devel';
    private const API_TOKEN = '12345';

    private $client;

    public function __construct($isTest = false)
    {
        $this->client = new Client([
            'base_uri' => $isTest? self::TEST_API_URI: self::API_URI,
        ]);
    }


    private function getHeaders(array $additionalHeaders = [])
    {
        $headers = [
            'Authorization' => self::API_TOKEN,
            'Content-Type' => 'application/json'
        ];

        return array_merge($headers, $additionalHeaders);
    }

    public function getProductByVendorCode(array $vendorCodes)
    {
        $url = '/content/v1/cards/filter';
        $result = [];
        foreach(array_chunk($vendorCodes, 100) as $chunk){
            $requestParams = [
                'headers' => $this->getHeaders(),
                'form_params' => [
                    'vendorCodes' =>$vendorCodes
                ]
            ];
            try {
                $response = $this->client->request('GET', $url, $requestParams);
                $body = json_decode($response->getBody(), 1);
                foreach($body['data'] as $product){
                    $result[] = $product;
                }
            } catch (TransferException $e) {
                echo Psr7\Message::toString($e->getRequest());
                echo Psr7\Message::toString($e->getResponse());
            }
        }

        return $result;
    }
}