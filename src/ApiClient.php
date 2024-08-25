<?php

namespace Timurrodya\Enkod;

use Exception;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Timurrodya\Enkod\Exceptions\ApiException;

/**
 * Class ApiClient
 *
 * @package Timurrodya\Enkod
 */
class ApiClient
{
    public PendingRequest $client;

    public function __construct()
    {
        $baseUrl = sprintf(
            "%s/%s",
            rtrim((string) config('enkod.baseurl'), '/'),
            rtrim(config('enkod.version'), '/'),
        );

        $this->client = Http::withHeaders([
            'apiKey' => config('enkod.apiKey'),
            'Accept' => 'application/json',
        ])
            ->baseUrl($baseUrl)
            ->retry(3, 100, fn($exception): bool => $exception instanceof ConnectionException)
            ->acceptJson()
            ->contentType('application/json');
    }

    /**
     * @throws Exception
     */
    public function request(string $method, string $url, array $data = []): PromiseInterface|Response
    {
        try {
            return $this->client->withBody(json_encode($data), 'json')->send($method, $url);
        } catch (RequestException $e) {
            throw new ApiException($e->getCode(), $e->response->json('message'));
        }
    }
}
