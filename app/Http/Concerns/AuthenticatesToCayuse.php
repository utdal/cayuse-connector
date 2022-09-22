<?php

namespace App\Http\Concerns;

use App\Http\Components\Authenticator;
use Symfony\Component\HttpClient\HttpClient;

trait AuthenticatesToCayuse
{
    public ?Authenticator $auth = null;

    public function login(): bool
    {
        if (!($this->auth instanceof Authenticator)) {
            $this->setAuthenticator();
        }

        return $this->auth->authenticate();
    }

    public function setAuthenticator(Authenticator $auth = null): static
    {
        $this->auth = $auth ?? new Authenticator();

        return $this;
    }

    public function authenticatedClientOptions(): array
    {
        $this->login();

        return [
            'auth_bearer' => $this->auth->auth_token,
            'headers' => [
                'Content-Type' => 'application/json',
                'X-IDP-New-Login' => 'true',
            ],
        ];
    }
}