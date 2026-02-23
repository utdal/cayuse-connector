<?php

namespace App\Config;

class Config
{
    public static $server_settings = [
        'CAYUSE_TENANT_ID',
        'CAYUSE_AUTH_SERVER',
        'CAYUSE_API_SERVER',
        'CAYUSE_HR_CONNECT_SERVER',
        'CAYUSE_USERNAME',
        'CAYUSE_PASSWORD',
    ];

    public static function get(?string $setting = null): string|array|false
    {
        if ($setting && getenv('CAYUSE_SERVER_ENVIRONMENT') === 'UAT' && in_array($setting, static::$server_settings, true)) {
            $setting = "UAT_$setting";
        }

        return getenv($setting);
    }

    public static function set(string $setting, string $value): bool
    {
        return putenv("$setting=$value");
    }
}