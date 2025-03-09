<?php

namespace infra;

use domain\course\Course;
use domain\group\Group;
use Illuminate\Support\Facades\DB;
use domain\group\GroupPersistenceInterface;
use domain\user\User;

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
            ->where('g.status', 'ativo')
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
            ->get()
            ->toArray()
        ;
    }
}
