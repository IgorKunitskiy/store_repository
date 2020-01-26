<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderStatusPolicy
{
    use HandlesAuthorization;

    public function changeStatus(User $user)
    {
        return $user->role === 'admin';
    }

    public function index(User $user)
    {
        return true;
    }
}
