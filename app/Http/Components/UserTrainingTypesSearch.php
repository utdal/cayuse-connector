<?php

namespace App\Http\Components;

use App\Http\Concerns\AuthenticatesToCayuse;
use App\Http\Concerns\HasSearchQuery;
use Symfony\Component\HttpClient\HttpClient;

class UserTrainingTypesSearch
{
    use AuthenticatesToCayuse;
    use HasSearchQuery;

    public string $api_server = '';
    public string $api_path = '/v1/training-types';
    public array $api_search_queries = [
        'name' => 'name',
        'active' => 'active',
    ];

    public function __construct()
    {
        $this->api_server = getenv('CAYUSE_API_SERVER') ?? '';
    }

    public function search(string $name = '', ?bool $active = null): array
    {
        return HttpClient::create($this->authenticatedClientOptions())
            ->request('GET', "{$this->api_server}{$this->api_path}", [
                'query' => $this->buildSearchQuery([
                    'name' => $name,
                    'active' => ($active ? 'true' : 'false'),
                ]),
            ])
            ->toArray();
    }
}