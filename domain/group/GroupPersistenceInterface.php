<?php

namespace domain\group;

use domain\group\Group;

interface GroupPersistenceInterface
{
    public function loadAllGroups(Group $group): array;
    public function loadGroupByUserId(Group $group): bool;
}