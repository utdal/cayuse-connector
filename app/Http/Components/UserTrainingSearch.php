<?php

namespace App\Http\Components;

use App\Http\Components\UserSearch;
use App\Http\Concerns\AuthenticatesToCayuse;
use Symfony\Component\HttpClient\HttpClient;

class UserTrainingSearch
{
    use AuthenticatesToCayuse;

    public string $api_server = '';
    public string $api_path = '/v1/people/%s/trainings';

    public function __construct()
    {
        $this->api_server = getenv('CAYUSE_API_SERVER') ?? '';
    }

    public function search(array $queries): array
    {
        $this->login();
        $user_search_result = (new UserSearch())->setAuthenticator($this->auth)->search($queries);

        array_walk($user_search_result['people'], function(&$user) {
            if (isset($user['id'])) {
                $user['trainings'] = HttpClient::create($this->authenticatedClientOptions())
                    ->request('GET', $this->api_server . sprintf($this->api_path, $user['id']))
                    ->toArray();
            }
        });

        return $user_search_result;
    }
}