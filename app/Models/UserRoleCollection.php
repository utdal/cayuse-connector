<?php

namespace App\Models;

class UserRoleCollection
{
    public $roles = [];

    public function __construct(public array $role_options)
    {
        foreach ($this->role_options['roles'] ?? [] as $role) {
            $this->roles[] = new UserRole(
                name: $role,
                unit_primary_code: $this->role_options['unit_codes'][$role] ?? '',
                include_subunits: in_array($role, $this->role_options['subunits'])
            );
        }
    }

    public function all(): array
    {
        return $this->roles;
    }

    public function isEmpty(): bool
    {
        return empty($this->roles);
    }

    public function isNotEmpty(): bool
    {
        return !$this->isEmpty();
    }
}