<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SetsEnvironment
{
    public array $original_env = [];

    public function process(Request $request, callable $next): Response
    {
        $this->setEnv(
            env_name: 'CAYUSE_SERVER_ENVIRONMENT',
            override: $request->query->get('environment') ?? $request->request->get('environment'),
            allowed: ['UAT', 'production']
        );

        return $next($request);

        $this->restoreEnv();
    }

    public function setEnv(string $env_name, ?string $override, array $allowed = []): bool
    {
        if (!empty($allowed) && !in_array($override, $allowed, true)) {
            return false;
        }

        if ($override !== getenv($env_name)) {
            $this->original_env[$env_name] = getenv($env_name);
            return putenv("$env_name=$override");
        }

        return false;
    }

    public function restoreEnv(): void
    {
        foreach ($this->original_env as $env_name => $original_value) {
            putenv("$env_name=$original_value");
        }
    }
}