<?php

namespace domain\course;

interface CoursePersistenceInterface
{
    public function loadById(Course $course): bool;
}