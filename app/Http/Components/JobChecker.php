<?php

namespace App\Http\Components;

use App\Files\CsvReader;
use App\Http\Concerns\AuthenticatesToCayuse;
use Symfony\Component\HttpClient\{
    Exception\ClientException,
    Exception\JsonException,
    HttpClient,
};

class JobChecker
{
    use AuthenticatesToCayuse;

    public string $api_server = '';
    public string $api_path = '/api/v2/administration/batch/%s/%s';

    public function __construct(public string $check_type, public string $job_type)
    {
        $this->api_server = getenv('CAYUSE_HR_CONNECT_SERVER') ?? '';
    }

    public function check(string $job_id): array
    {
        $response = HttpClient::create($this->authenticatedClientOptions())
            ->request('GET', $this->api_server . sprintf($this->api_path, $this->check_type, $this->job_type), [
                'query' => ['jobId' => $job_id],
            ]);

        try {
            $result = $response->toArray();
        } catch (ClientException $e) {
            $result = $e->getResponse()->toArray(false);
        } catch (JsonException $e) {
            $reader = new CsvReader($response->getContent());
            $result = [
                'content' => array_values($reader->toArray()),
                'html' => $reader->toHtml(),
            ];
        } finally {
            $result['response_code'] = $response->getStatusCode();
        }

        return $result;
    }
}