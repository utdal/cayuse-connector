<?php

namespace App\Models;

class UserRole
{
    public function __construct(
        public string $name,
        public string $unit_primary_code,
        protected bool $include_subunits,
    ) {
        //
    }

    public function getIncludeSubunitsProperty(): string
    {
        if (!$this->unit_primary_code) {
            return '';
        }

        return $this->include_subunits ? 'Yes' : 'No';
    }

    public function __get($name)
    {
        if ($name === 'include_subunits') {
            return $this->getIncludeSubunitsProperty();
        }
    }
}