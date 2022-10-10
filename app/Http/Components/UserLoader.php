<?php

namespace App\Http\Components;

use App\Http\Concerns\AuthenticatesToCayuse;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

class UserLoader
{
    use AuthenticatesToCayuse;

    public string $api_server = '';
    public string $api_path = '/api/v2/administration/batch/upload/user';

    public function __construct()
    {
        $this->api_server = getenv('CAYUSE_HR_CONNECT_SERVER') ?? '';
    }

    public function load(UploadedFile $file): array
    {
        $request_options = $this->authenticatedClientOptions();

        $form_data = new FormDataPart([
            'file' => DataPart::fromPath($file->getPathname()),
        ]);
        $form_data->getHeaders()->addTextHeader('X-IDP-New-Login', 'true');

        $request_options['headers'] = $form_data->getPreparedHeaders()->toArray();

        return HttpClient::create($request_options)
            ->request('POST', "{$this->api_server}{$this->api_path}", [
                'query' => [
                    'send_account_activation_emails' => 'false',
                ],
                'body' => $form_data->bodyToIterable(),
            ])
            ->toArray();
    }
}