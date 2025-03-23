<?php

namespace domain\group;

use domain\group\Group;

interface GroupPersistenceInterface
{
    public function create(Group $group): Group;
    public function insertPendingTeammate(Group $group): Group;
    public function insertTeammate(Group $group): Group;
    public function isUserRequestedToJoinGroup(Group $group): bool;
    public function loadAllGroups(Group $group): array;
    public function loadGroupByUserId(Group $group): bool;
    public function loadGroupById(Group $group): bool;
    public function approveRequest(Group $group): void;
    public function rejectRequest(Group $group): void;
    public function isUserAlreadyInGroup(Group $group): bool;
}