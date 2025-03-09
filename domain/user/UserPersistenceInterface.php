<?php

namespace domain\user;

use domain\user\User;

interface UserPersistenceInterface
{
    public function load(User $user): User;
    public function loadById(User $user): bool;
}