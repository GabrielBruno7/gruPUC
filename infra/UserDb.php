<?php

namespace infra;

use domain\course\Course;
use domain\user\User;
use domain\user\UserPersistenceInterface;
use Illuminate\Support\Facades\DB;

class UserDb implements UserPersistenceInterface
{
    public function load(User $user): User
    {
        return $user;
    }

    public function loadById(User $user): bool
    {
        $result = DB::table('users')
            ->where('users.id', $user->getId())
            ->first()
        ;

        if (!$result) {
            return false;
        }

        $course = (new Course(new CourseDb()))
            ->setId($result->course_id)
            ->loadById()
        ;

        $user
            ->setName($result->name)
            ->setEmail($result->email)
            ->setRole($result->role)
            ->setCourse($course)
        ;

        return true;
    }
}
