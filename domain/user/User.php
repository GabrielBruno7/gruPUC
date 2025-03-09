<?php

namespace domain\user;

use domain\course\Course;
use domain\group\Group;
use Exception;

class User
{
    private string $id;
    private int $period;
    private string $name;
    private string $role;
    private Group $group;
    private string $email;
    private Course $course;
    private ?UserPersistenceInterface $persistence = null;

    public function __construct(UserPersistenceInterface $persistence)
    {
        $this->persistence = $persistence;
    }

    public function getPersistence(): UserPersistenceInterface
    {
        return $this->persistence;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setPeriod(int $period): self
    {
        $this->period = $period;

        return $this;
    }

    public function getPeriod(): int
    {
        return $this->period;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setGroup(Group $group): self
    {
        $this->group = $group;

        return $this;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setCourse(Course $course): self
    {
        $this->course = $course;

        return $this;
    }

    public function getCourse(): Course
    {
        return $this->course;
    }

    public function loadById(): User
    {
        if (!$this->getPersistence()->loadById($this)) {
            throw new Exception("Usuário não encontrado.");
        }

        return $this;
    }
}

