<?php

namespace App\Http\Components;

use App\Http\Concerns\AuthenticatesToCayuse;
use App\Http\Concerns\HasSearchQuery;
use Symfony\Component\HttpClient\HttpClient;

class UnitSearch
{
    use AuthenticatesToCayuse;
    use HasSearchQuery;

    public string $api_server = '';
    public string $api_path = '/v1/units';
    public array $api_search_queries = [
        'name' => 'unitName',
        'code' => 'primaryCode',
    ];

    public function __construct()
    {
        $this->api_server = getenv('CAYUSE_API_SERVER') ?? '';
    }

    public function search(array $queries): array
    {
        return HttpClient::create($this->authenticatedClientOptions())
            ->request('GET', "{$this->api_server}{$this->api_path}", [
                'query' => $this->buildSearchQuery($queries),
            ])
            ->toArray();
    }
}