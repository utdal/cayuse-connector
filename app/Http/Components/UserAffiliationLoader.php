<?php

namespace App\Http\Components;

use App\Http\Concerns\AuthenticatesToCayuse;
use App\Http\Concerns\FiltersCsvColumns;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserAffiliationLoader
{
    use AuthenticatesToCayuse;
    use FiltersCsvColumns;

    public string $api_server = '';
    public string $api_path = '/api/v2/administration/batch/upload/affiliation';
    public array $default_columns = [
        'Username',
        'Employee ID',
        'Unit Primary Code',
        'Unit Name',
        'Title',
        'Primary Appointment',
        'US Government Agency',
        'Contact Email',
        'Contact Office Phone',
        'Contact Mobile Phone',
        'Contact Pager',
        'Contact Fax',
        'Contact Preferred Contact Method',
        'Contact Street 1',
        'Contact Street 2',
        'Contact County',
        'Contact Country',
        'Contact State/Province',
        'Contact City',
        'Contact Postal Code',
        'Contact Website',
        'Performance Site Organization',
        'Performance Site Active',
        'Performance Site DUNS',
        'Performance Site Email',
        'Performance Site Office Phone',
        'Performance Site Mobile Phone',
        'Performance Site Pager',
        'Performance Site Fax',
        'Performance Site Preferred Contact Method',
        'Performance Site Street 1',
        'Performance Site Street 2',
        'Performance Site County',
        'Performance Site Country',
        'Performance Site State/Province',
        'Performance Site City',
        'Performance Site Postal Code',
        'Performance Site Website',
        'Performance Site Congressional District',
        'Calendar Months',
        'Calendar Salary',
        'Academic Months',
        'Academic Salary',
        'Summer Months',
        'Summer Salary',
        'Principal Investigator',
        'Assistant',
        'Administration Official',
        'Signing Official',
        'Payee',
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
                'body' => ($filter_columns ? $this->filterCsvFile($file) : file_get_contents($file->getPathname())),
            ])
            ->toArray();
    }
}