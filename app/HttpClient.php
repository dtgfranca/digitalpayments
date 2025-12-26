<?php

namespace App;

trait HttpClient
{
    public function getContents(\Illuminate\Http\Client\Response $response): array
    {
        return json_decode($response->getBody()->getContents(), true);
    }
}
