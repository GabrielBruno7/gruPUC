<?php

namespace domain\invite;

use domain\user\User;
use domain\group\Group;
use domain\helper\Helper;
use Illuminate\Validation\ValidationException;

class Invite
{
    private string $id;
    private User $sender;
    private Group $group;
    private string $status;
    private User $recipient;
    private string $createdAt;
    private ?InvitePersistenceInterface $persistence;

    public const INVITE_STATUS_PENDING = 'pending';
    public const INVITE_STATUS_APPROVED = 'approved';
    public const INVITE_STATUS_CANCELLED = 'cancelled';

    public function __construct(?InvitePersistenceInterface $persistence = null)
    {
        $this->persistence = $persistence;
    }

    public function getPersistence(): InvitePersistenceInterface
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

    public function setSender(User $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getSender(): User
    {
        return $this->sender;
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

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setRecipient(User $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getRecipient(): User
    {
        return $this->recipient;
    }

    public function setCreatedAt(string $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function inviteTeammates(): void
    {
        $teammates = $this->getGroup()->getTeammates();

        if ($teammates) {
            foreach ($teammates as $teammateEmail) {
                $teammate = $this->getGroup()->getUser()
                    ->setEmail($teammateEmail)
                    ->loadByEmail()
                ;

                if (!$teammate) {
                    throw ValidationException::withMessages([
                        'members' => "O usuário com o email $teammateEmail não foi encontrado.",
                    ]);
                }

                $this
                    ->setCreatedAt(now())
                    ->setId(Helper::uuid())
                    ->setRecipient($teammate)
                    ->setStatus(self::INVITE_STATUS_PENDING)
                    ->setSender($this->getGroup()->getOwnerUser())
                ;

                $this->getPersistence()->inviteTeammate($this);
            }
        }
    }
}

