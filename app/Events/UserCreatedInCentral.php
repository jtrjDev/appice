<?php

namespace App\Events;

use App\Models\Central\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserCreatedInCentral
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User $user
    ) {}
}