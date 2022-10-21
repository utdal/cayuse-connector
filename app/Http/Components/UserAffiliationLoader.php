<?php

namespace App\Http\Components;

use App\Http\Concerns\AuthenticatesToCayuse;
use Symfony\Component\HttpClient\Exception\ServerException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\NativeHttpClient;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

class UserAffiliationLoader
{
    use AuthenticatesToCayuse;

    public string $api_server = '';
    public string $api_path = '/api/v2/administration/batch/upload/affiliation';

    public function __construct()
    {
        $this->api_server = getenv('CAYUSE_HR_CONNECT_SERVER') ?? '';
    }

    public function load(UploadedFile $file): array
    {
        $request_options = $this->authenticatedClientOptions();
        $request_options['headers']['Content-Type'] = 'text/csv';

        return HttpClient::create($request_options)
            ->request('POST', "{$this->api_server}{$this->api_path}", [
                'body' => file_get_contents($file->getPathname()),
            ])
            ->toArray();
    }
}