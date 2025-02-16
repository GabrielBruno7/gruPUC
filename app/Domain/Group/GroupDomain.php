<?php

namespace App\Domain\Group;

class GroupDomain
{
    public const COURSE_TYPE_ADS = 'ADS';
    public const COURSE_TYPE_MEDICINE = 'Medicina';
    public const COURSE_TYPE_LAW = 'Direito';
    public const COURSE_TYPE_IT = 'Informática';

    public const COURSES_SELECT = [
        self::COURSE_TYPE_ADS => self::COURSE_TYPE_ADS,
        self::COURSE_TYPE_MEDICINE => self::COURSE_TYPE_MEDICINE,
        self::COURSE_TYPE_LAW => self::COURSE_TYPE_LAW,
        self::COURSE_TYPE_IT => self::COURSE_TYPE_IT,
    ];

    public static function buildCourseSelect(): array
    {
        return self::COURSES_SELECT;
    }
}
