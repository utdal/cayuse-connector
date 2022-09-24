<?php

namespace App\Http\Components;

use App\Http\Concerns\AuthenticatesToCayuse;
use Carbon\Carbon;
use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;

class UserTrainingLoader
{
    use AuthenticatesToCayuse;

    public string $api_server = '';
    public string $api_post_path = '/v1/people/%s/trainings';
    public string $api_update_path = '/v1/people/%s/trainings/%s';
    public string $training_employeeId_key = 'Net ID';
    public string $training_name_key = 'Name';
    public string $training_completion_key = 'Approval Date';
    public string $training_expiration_key = 'Expires';
    public string $timezone = 'America/Chicago';
    protected string $training_type_id;

    public function __construct(string $training_type)
    {
        $this->api_server = getenv('CAYUSE_API_SERVER') ?? '';
        $this->training_type_id = $training_type;
    }

    public function load(array $training): array
    {
        $this->login();

        $user_employeeId = $training[$this->training_employeeId_key] ?? false;
        $name = $training[$this->training_name_key] ?? '';

        if (!$user_employeeId) {
            return $this->error("No employee ID provided for $name.");
        }

        // check for existing user training
        $user_training_search_result = $this->searchUserTrainings($user_employeeId);
        $user_id = $user_training_search_result['user_id'] ?? false;
        $existing_trainings = $user_training_search_result['trainings'] ?? [];

        if (!$user_id) {
            return $this->error("Unable to find user $name ($user_employeeId) in Cayuse.");
        }

        $existing_training = $this->existingTraining($existing_trainings);

        // add or update training
        $cayuse_client = HttpClient::create($this->authenticatedClientOptions());
        try {
            if ($existing_training) {
                $result = $cayuse_client->request('PUT', $this->api_server . sprintf($this->api_update_path, $user_id, $existing_training['id']), [
                    'json' => [
                        "id" => $existing_training['id'],
                        "objectVersion" => $existing_training['objectVersion'],
                        "trainingTypeId" => $existing_training['trainingTypeId'],
                        "completionDate" => $this->formatDate($training[$this->training_completion_key]),
                        "expirationDate" => $this->formatDate($training[$this->training_expiration_key]),
                    ],
                ]);
            } else {
                $result = $cayuse_client->request('POST', $this->api_server . sprintf($this->api_post_path, $user_id), [
                    'json' => [
                        "trainingTypeId" => $this->training_type_id,
                        "completionDate" => $this->formatDate($training[$this->training_completion_key]),
                        "expirationDate" => $this->formatDate($training[$this->training_expiration_key]),
                    ],
                ]);
            }
            $result_array = $result->toArray();
        } catch (ClientException $e) {
            $result_array = $e->getResponse()->toArray(false);
            $message = $e->getMessage();
        }

        return [
            'status' => match ($result->getStatusCode()) {
                200, 201 => 'ok',
                400 => 'bad request',
                401 => 'token missing or invalid',
                403 => 'permission denied',
                404 => 'not found',
                409 => 'update conflict',
                412 => 'precondition failed',
                422 => 'validation failed',
            },
            'message' => $message ?? "Updated training for $name ($user_employeeId).",
            'results' => $result_array,
            'existing_training' => $existing_training,
            'new_training' => $training,
        ];
    }

    public function searchUserTrainings(string $user_employeeId): array
    {
        return (new UserTrainingSearch())
            ->setAuthenticator($this->auth)
            ->search($user_employeeId);
    }

    public function existingTraining(array $existing_trainings): ?array
    {
        foreach($existing_trainings as $existing_training) {
            if ($existing_training['trainingTypeId'] === $this->training_type_id) {
                return $existing_training;
            }
        }

        return null;
    }

    public function formatDate(string $date): string
    {
        return (new Carbon($date, $this->timezone))->toAtomString();
    }

    public function error(string $message): array
    {
        return [
            'response' => null,
            'status' => 'error',
            'message' => $message,
        ];
    }
}