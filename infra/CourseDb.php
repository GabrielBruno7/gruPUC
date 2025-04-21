<?php

namespace infra;

use domain\course\Course;
use Illuminate\Support\Facades\DB;
use domain\course\CoursePersistenceInterface;

class CourseDb implements CoursePersistenceInterface
{
    private const COURSE_TABLE = 'courses';

    public function loadById(Course $course): bool
    {
        $result = DB::table(SELF::COURSE_TABLE)
            ->where('id', $course->getId())
            ->first()
        ;

        if (!$result) {
            return false;
        }

        $course
            ->setName($result->name)
            ->setType($result->type)
            ->setPeriod($result->period)
        ;

        return true;
    }

    public function findAllCourses(): array
    {
        return DB::table('courses')
            ->get()
            ->toArray()
        ;
    }
}
