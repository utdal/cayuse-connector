<?php

namespace App\Http\Components;

use App\Files\CsvReader;
use App\Http\Concerns\AuthenticatesToCayuse;
use App\Models\UserRoleCollection;
use League\Csv\Writer;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserRoleLoader
{
    use AuthenticatesToCayuse;

    public string $api_server = '';
    public string $api_path = '/api/v2/administration/batch/upload/role';
    public array $default_columns = [
        'Username',
        'Role',
        'Unit Primary Code',
        'Include All Sub Units',
    ];

    public function __construct()
    {
        $this->api_server = getenv('CAYUSE_HR_CONNECT_SERVER') ?? '';
    }

    public function load(UploadedFile $file, UserRoleCollection $roles): array
    {
        $request_options = $this->authenticatedClientOptions();
        $request_options['headers']['Content-Type'] = 'text/csv';

        return HttpClient::create($request_options)
            ->request('POST', "{$this->api_server}{$this->api_path}", [
                'body' => $roles->isNotEmpty() ? $this->buildCsv($file, $roles) : file_get_contents($file->getPathname()),
            ])
            ->toArray();
    }

    public function buildCsv(UploadedFile $file, UserRoleCollection $roles): string
    {
        $rows = [
            $this->default_columns, // header row
        ];

        foreach ((new CsvReader($file))->rows as $row) {
            foreach ($roles->all() as $role) {
                $rows[] = [
                    'Username' => $row['Username'],
                    'Role' => $role->name,
                    'Unit Primary Code' => $role->unit_primary_code,
                    'Include All Sub Units' => $role->include_subunits,
                ];
            }
        }

        $csv = Writer::createFromString('');
        $csv->insertAll($rows);

        return $csv->toString();
    }

}