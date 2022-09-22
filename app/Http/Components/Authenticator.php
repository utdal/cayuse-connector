<?php

namespace App\Http\Components;

use Symfony\Component\HttpClient\HttpClient;

class Authenticator
{
    public string $auth_server = '';
    public string $auth_path = '/basicauth';
    public string $auth_username = '';
    public string $auth_password = '';
    public string $auth_token = '';
    public string $tenant_id = '';

    public function __construct()
    {
        $this->auth_server = getenv('CAYUSE_AUTH_SERVER') ?? '';
        $this->auth_username = getenv('CAYUSE_USERNAME') ?? '';
        $this->auth_password = getenv('CAYUSE_PASSWORD') ?? '';
        $this->tenant_id = getenv('CAYUSE_TENANT_ID') ?? '';
    }

    public function authenticate(): bool
    {
        if ($this->isAuthenticated()) {
            return true;
        }

        $this->auth_token = HttpClient::create()
            ->request('GET', "{$this->auth_server}{$this->auth_path}", [
                'auth_basic' => [$this->auth_username, $this->auth_password],
                'query' => ['tenant_id' => $this->tenant_id],
            ])
            ->getContent();

        return true;
    }

    public function isAuthenticated(): bool
    {
        return $this->auth_token !== '';
    }

    public function authenticatedClientOptions(): array
    {
        if (!$this->isAuthenticated()) {
            $this->authenticate();
        }

        return [
            'auth_bearer' => $this->auth_token,
            'headers' => [
                'Content-Type' => 'application/json',
                'X-IDP-New-Login' => 'true',
            ],
        ];
    }

    public function logout(): void
    {
        $this->auth_token = '';
    }
}