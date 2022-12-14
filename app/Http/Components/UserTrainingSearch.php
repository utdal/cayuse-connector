<?php

namespace App\Http\Components;

use App\Http\Components\UserSearch;
use App\Http\Concerns\AuthenticatesToCayuse;
use App\Http\Concerns\FixesJson;
use JsonException;
use Symfony\Component\HttpClient\HttpClient;

class UserTrainingSearch
{
    use AuthenticatesToCayuse;
    use FixesJson;

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
                $request = HttpClient::create($this->authenticatedClientOptions())
                        ->request('GET', $this->api_server . sprintf($this->api_path, $user['id']));
                try {
                    $user['trainings'] = $request->toArray();
                } catch (JsonException $e) {
                    $fixed_json = $this->fixJson($request->getContent());
                    $user['trainings'] = json_decode($fixed_json, true, 512, \JSON_BIGINT_AS_STRING | \JSON_THROW_ON_ERROR);
                }
            }
        });

        return $user_search_result;
    }
}