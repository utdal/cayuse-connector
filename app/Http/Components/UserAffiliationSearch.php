<?php

namespace App\Http\Components;

use App\Http\Components\UserSearch;
use App\Http\Concerns\AuthenticatesToCayuse;
use Symfony\Component\HttpClient\HttpClient;

class UserAffiliationSearch
{
    use AuthenticatesToCayuse;

    public string $api_server = '';
    public string $api_path = '/v1/people/%s/internal-affiliation';

    public function __construct()
    {
        $this->api_server = getenv('CAYUSE_API_SERVER') ?? '';
    }

    public function search(string $query): array
    {
        $this->login();
        $user_search_result = (new UserSearch())->setAuthenticator($this->auth)->search($query);
        $user_id = $user_search_result['people'][0]['id'] ?? false;

        if (!$user_id) {
            return [];
        }

        return HttpClient::create($this->authenticatedClientOptions())
            ->request('GET', $this->api_server . sprintf($this->api_path, $user_id))
            ->toArray() + ['user' => ($user_search_result['people'][0] ?? null)];
    }
}