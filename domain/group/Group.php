<?php

namespace domain\group;

use domain\user\User;
use Exception;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class Group
{
    private User $user;
    private string $id;
    private int $period;
    private string $name;
    private User $ownerUser;
    private ?array $teammates;
    private \DateTime $createdAt;
    private ?GroupPersistenceInterface $persistence = null;

    public const GROUP_STATUS_ACTIVE = 'ativo';

    public function __construct(GroupPersistenceInterface $persistence)
    {
        $this->persistence = $persistence;
    }

    public function getPersistence(): GroupPersistenceInterface
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

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setOwnerUser(User $ownerUser): self
    {
        $this->ownerUser = $ownerUser;

        return $this;
    }

    public function getOwnerUser(): User
    {
        return $this->ownerUser;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setTeammates(?array $teammates): self
    {
        $this->teammates = $teammates;

        return $this;
    }

    public function getTeammates(): ?array
    {
        return $this->teammates;
    }

    public function loadAllGroups(): array
    {
        return $this->getPersistence()->loadAllGroups($this);
    }

    public function loadGroupByUserId(): Group
    {
        if (!$this->getPersistence()->loadGroupByUserId($this)) {
            throw new Exception("Grupo não encontrado para o usuário.");
        }

        return $this;
    }

    public function create(): self
    {
        $this
            ->setId(Uuid::uuid4()->toString())
            ->setCreatedAt(now())
        ;

        return $this->getPersistence()->create($this);
    }
}

