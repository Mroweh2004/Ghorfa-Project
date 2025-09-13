<?php

namespace App\Traits;

trait HasAdminRole
{
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }
} 