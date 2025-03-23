<?php

namespace domain\user;

use domain\user\User;

interface UserPersistenceInterface
{
    public function load(User $user): User;
    public function loadById(User $user): bool;
    public function loadByEmail(User $user): bool;
    public function isUserGroupFounder(User $user): bool;
    public function isUserAlreadyHaveAGroup(User $user): bool;
}