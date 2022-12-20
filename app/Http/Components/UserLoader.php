<?php

namespace App\Http\Components;

use App\Http\Concerns\AuthenticatesToCayuse;
use App\Http\Concerns\FiltersCsvColumns;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserLoader
{
    use AuthenticatesToCayuse;
    use FiltersCsvColumns;

    public string $api_server = '';
    public string $api_path = '/api/v2/administration/batch/upload/user';
    public array $default_columns = [
        'First Name',
        'Middle Name',
        'Last Name',
        'Active',
        'Employee ID',
        'Email',
        'Username',
        'Create Account',
        'User Active',
    ];

    public function __construct()
    {
        $this->api_server = getenv('CAYUSE_HR_CONNECT_SERVER') ?? '';
    }

    public function load(UploadedFile $file, bool $filter_columns = true): array
    {
        $request_options = $this->authenticatedClientOptions();
        $request_options['headers']['Content-Type'] = 'text/csv';

        return HttpClient::create($request_options)
            ->request('POST', "{$this->api_server}{$this->api_path}", [
                'query' => [
                    'send_account_activation_emails' => 'false',
                ],
                'body' => ($filter_columns ? $this->filterCsvFile($file) : file_get_contents($file->getPathname())),
            ])
            ->toArray();
    }
}