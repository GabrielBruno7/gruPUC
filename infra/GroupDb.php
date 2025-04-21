<?php

namespace infra;

use Exception;
use Ramsey\Uuid\Uuid;
use domain\user\User;
use domain\group\Group;
use Illuminate\Support\Facades\DB;
use domain\group\GroupPersistenceInterface;
use domain\helper\Helper;

class GroupDb implements GroupPersistenceInterface
{
    public function loadAllGroups(Group $group): array
    {
        return DB::table('groups as g')
            ->select(
                'g.id',
                'g.name',
                'g.period',
                'c.name as course_name',
                'u.name as owner_name'
            )
            ->leftJoin('courses as c', 'g.course_id', '=', 'c.id')
            ->leftJoin('users as u', 'g.owner_id', '=', 'u.id')
            ->whereNull('g.deleted_at')
            ->where('g.status', Group::GROUP_STATUS_ACTIVE)
            ->where('g.period', $group->getUser()->getPeriod())
            ->where('g.course_id', $group->getUser()->getCourse()->getId())
            ->get()
            ->toArray()
        ;
    }

    public function loadGroupByUserId(Group $group): bool
    {
        $result = DB::table('user_group as ug')
            ->select(
                'g.id',
                'g.name',
                'g.period',
                'g.owner_id',
                'g.created_at'
            )
            ->leftJoin('groups as g', 'ug.group_id', '=', 'g.id')
            ->leftJoin('users as u', 'g.owner_id', '=', 'u.id')
            ->leftJoin('courses as c', 'g.course_id', '=', 'c.id')
            ->where('ug.user_id', $group->getUser()->getId())
            ->first()
        ;

        if (!$result) {
            return false;
        }

        $ownerUser = (new User(new UserDb()))
            ->setId($result->owner_id)
            ->loadById()
        ;

        $group
            ->setId($result->id)
            ->setName($result->name)
            ->setPeriod($result->period)
            ->setOwnerUser($ownerUser)
            ->setCreatedAt((new \DateTime($result->created_at)))
        ;

        $teammates = $this->loadUsersByGroupId($group);

        $group->setTeammates($teammates);

        return true;
    }

    public function loadGroupById(Group $group): bool
    {
        $result = DB::table('user_group as ug')
            ->select(
                'g.id',
                'g.name',
                'g.period',
                'g.owner_id',
                'g.status',
                'g.created_at'
            )
            ->leftJoin('groups as g', 'ug.group_id', '=', 'g.id')
            ->leftJoin('users as u', 'g.owner_id', '=', 'u.id')
            ->leftJoin('courses as c', 'g.course_id', '=', 'c.id')
            ->where('g.id', $group->getId())
            ->first()
        ;

        if (!$result) {
            return false;
        }

        $ownerUser = (new User(new UserDb()))
            ->setId($result->owner_id)
            ->loadById()
        ;

        $group
            ->setId($result->id)
            ->setName($result->name)
            ->setPeriod($result->period)
            ->setStatus($result->status)
            ->setOwnerUser($ownerUser)
            ->setCreatedAt((new \DateTime($result->created_at)))
        ;

        $teammates = $this->loadUsersByGroupId($group);

        $group->setTeammates($teammates);

        return true;
    }

    private function loadUsersByGroupId(Group $group): ?array
    {
        return DB::table('user_group as ug')
            ->select(
                'u.id',
                'u.name',
                'u.email',
                'ug.status',
            )
            ->leftJoin('users as u', 'ug.user_id', '=', 'u.id')
            ->where('ug.group_id', $group->getId())
            ->whereIn(
                'ug.status',
                [
                    User::USER_STATUS_APPROVED,
                    User::USER_STATUS_PENDING
                ]
            )
            ->get()
            ->toArray()
        ;
    }

    public function isUserAlreadyInGroup(Group $group): bool
    {
        return DB::table('user_group as ug')
            ->leftJoin('users as u', 'ug.user_id', '=', 'u.id')
            ->where('ug.user_id', $group->getUser()->getId())
            ->where('ug.status', User::USER_STATUS_APPROVED)
            ->exists()
        ;
    }

    public function hasActiveTeammatesOnGroup(Group $group): bool
    {
        return DB::table('user_group')
            ->join('groups', 'user_group.group_id', '=', 'groups.id')
            ->where('groups.status', 'ativo')
            ->where('groups.id', $group->getId())
            ->whereIn('user_group.status', ['pending', 'approved'])
            ->where('user_group.user_id', '!=', $group->getOwnerUser()->getId())
            ->whereNull('groups.deleted_at')
            ->exists()
        ;
    }

    public function isUserRequestedToJoinGroup(Group $group): bool
    {
        return DB::table('user_group')
            ->where('group_id', $group->getId())
            ->where('user_id', $group->getUser()->getId())
            ->where('status', User::USER_STATUS_PENDING)
            ->exists()
        ;
    }

    public function create(Group $group): Group
    {
        DB::transaction(function () use ($group) {
            $affectedRow1 = DB::table('groups')->insert([
                'id' => $group->getId(),
                'name' => $group->getName(),
                'period' => $group->getPeriod(),
                'status' => Group::GROUP_STATUS_ACTIVE,
                'created_at' => $group->getCreatedAt(),
                'owner_id' => $group->getOwnerUser()->getId(),
                'course_id' => $group->getOwnerUser()->getCourse()->getId(),
            ]);

            if (!$affectedRow1) {
                throw new Exception('Houve um erro na criação do grupo');
            }

            $affectedRow2 = DB::table('user_group')->insert([
                'created_at' => now(),
                'group_id' => $group->getId(),
                'id' => Uuid::uuid4()->toString(),
                'status' => User::USER_STATUS_APPROVED,
                'user_id' => $group->getOwnerUser()->getId(),
            ]);

            if (!$affectedRow2) {
                throw new Exception('Houve um erro na criação do vinculo do grupo com o usuário');
            }
        });

        return $group;
    }

    public function insertPendingTeammate(Group $group): Group
    {
        DB::transaction(function () use ($group) {
            $affectedRow = DB::table('user_group')->insert([
                'group_id' => $group->getId(),
                'id' => Helper::uuid(),
                'created_at' => $group->getCreatedAt(),
                'user_id' => $group->getUser()->getId(),
                'status' => Group::GROUP_STATUS_PENDING,
            ]);

            if (!$affectedRow) {
                throw new Exception('Houve um erro na criação do vinculo do grupo com o usuário');
            }
        });

        return $group;
    }

    public function insertTeammate(Group $group): Group
    {
        DB::transaction(function () use ($group) {
            $affectedRow = DB::table('user_group')->insert([
                'group_id' => $group->getId(),
                'id' => Helper::uuid(),
                'created_at' => $group->getCreatedAt(),
                'user_id' => $group->getUser()->getId(),
                'status' => Group::GROUP_STATUS_APPROVED,
            ]);

            if (!$affectedRow) {
                throw new Exception('Houve um erro na criação do vinculo do grupo com o usuário');
            }
        });

        return $group;
    }

    public function approveRequest(Group $group): void
    {
        $affectedRow = DB::table('user_group')
            ->where('group_id', $group->getId())
            ->where('user_id', $group->getUser()->getId())
            ->where('status', 'pending')
            ->update(['status' => User::USER_STATUS_APPROVED])
        ;

        if (!$affectedRow) {
            throw new Exception('Houve um erro na aprovação do usuário');
        }
    }

    public function deleteGroup(Group $group): void
    {
        $affectedRow = DB::table('groups')
            ->where('id', $group->getId())
            ->update(['status' => $group->getStatus()])
        ;

        if (!$affectedRow) {
            throw new Exception('Houve um erro ao deletar grupo');
        }
    }

    public function rejectRequest(Group $group): void
    {
        $affectedRow = DB::table('user_group')
            ->where('group_id', $group->getId())
            ->where('user_id', $group->getUser()->getId())
            ->update(['status' => User::USER_STATUS_REPROVED])
        ;

        if (!$affectedRow) {
            throw new Exception('Houve um erro na rejeição do usuário');
        }
    }
}
