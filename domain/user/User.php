<?php

namespace domain\user;

use domain\course\Course;
use domain\group\Group;
use domain\helper\Helper;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    private string $id;
    private int $period;
    private string $name;
    private string $role;
    private Group $group;
    private string $email;
    private Course $course;
    private string $password;
    private ?UserPersistenceInterface $persistence = null;

    public const USER_STATUS_ACTIVE = 'ativo';

    public const USER_STATUS_APPROVED = 'approved';
    public const USER_STATUS_REJECTED = 'rejected';
    public const USER_STATUS_REPROVED = 'reproved';
    public const USER_STATUS_PENDING = 'pending';

    public function __construct(?UserPersistenceInterface $persistence = null)
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

    public function setPassword(string $password): self
    {
        $password = Hash::make($password);

        $this->password = $password;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
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

    public function loadByEmail(): User
    {
        if (!$this->getPersistence()->loadByEmail($this)) {
            throw new Exception("Usuário não encontrado.");
        }

        return $this;
    }

    public function checkIfUserGroupFounder(): void
    {
        if ($this->getPersistence()->isUserGroupFounder($this)) {
            throw ValidationException::withMessages([
                'error' => 'Não foi possível criar um novo grupo, você já possui um grupo ativo',
            ]);
        }
    }

    public function isUserAlreadyHaveAGroup(): bool
    {
        return $this->getPersistence()->isUserAlreadyHaveAGroup($this);
    }

    public function create(): self
    {
        $this->setId(Helper::uuid());

        $this->getPersistence()->create($this);

        return $this;
    }
}

