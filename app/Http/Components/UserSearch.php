<?php

namespace App\Http\Components;

use App\Http\Concerns\AuthenticatesToCayuse;
use Symfony\Component\HttpClient\HttpClient;

class UserSearch
{
    use AuthenticatesToCayuse;

    public string $api_server = '';
    public string $api_path = '/v1/people';

    public function __construct()
    {
        $this->api_server = getenv('CAYUSE_API_SERVER') ?? '';
    }

    public function search(string $query): array
    {
        return HttpClient::create($this->authenticatedClientOptions())
            ->request('GET', "{$this->api_server}{$this->api_path}", [
                'query' => ['search' => "employeeId:$query"],
            ])
            ->toArray();
    }
}