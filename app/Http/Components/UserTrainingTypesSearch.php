<?php

namespace App\Http\Components;

use App\Http\Concerns\AuthenticatesToCayuse;
use Symfony\Component\HttpClient\HttpClient;

class UserTrainingTypesSearch
{
    use AuthenticatesToCayuse;

    public string $api_server = '';
    public string $api_path = '/v1/training-types';

    public function __construct()
    {
        $this->api_server = getenv('CAYUSE_API_SERVER') ?? '';
    }

    public function search(string $name = '', ?bool $active = null): array
    {
        $search = [];

        if ($name) {
            $search[] = "name:$name";
        }
        if ($active) {
            $search[] = 'active:' . ($active ? 'true' : 'false');
        }

        return HttpClient::create($this->authenticatedClientOptions())
            ->request('GET', "{$this->api_server}{$this->api_path}", [
                'query' => count($search) ? ['search' => implode(',', $search)] : [],
            ])
            ->toArray();
    }
}