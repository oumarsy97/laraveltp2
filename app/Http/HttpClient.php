<?php

namespace App\Services\Http;

use App\Interfaces\ClientInterface;
use App\Repository\ClientRepository\ClientInterface as ClientRepositoryClientInterface;
use Illuminate\Support\Facades\Http;

class HttpClient implements ClientRepositoryClientInterface
{
    public function get($url, $params = [])
    {
        return Http::get($url, $params);
    }

    public function post($url, $data = [])
    {
        return Http::post($url, $data);
    }

    public function put($url, $data = [])
    {
        return Http::put($url, $data);
    }

    public function delete($url)
    {
        return Http::delete($url);
    }
}
