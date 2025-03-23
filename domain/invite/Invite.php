<?php

namespace domain\invite;

use domain\user\User;
use domain\group\Group;
use domain\helper\Helper;
use Exception;
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
    public const INVITE_STATUS_CANCELLED = 'cancelled';
    public const INVITE_STATUS_REJECTED = 'rejected';
    public const INVITE_STATUS_ACCEPTED = 'accepted';

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

    public function loadInviteById(): self
    {
        if (!$this->getPersistence()->loadInviteById($this)) {
            throw new Exception("Convite não encontrado.");
        }

        return $this;
    }

    public function checkIfAuthUserIsRealReceiver(string $email): self
    {
        if ($this->getRecipient()->getEmail() !== $email) {
            throw new Exception("O Convite só pode ser aceito por quem o recebeu.");
        }

        return $this;
    }

    public function loadPendingInvitesByRecipientEmail()
    {
        return $this->getPersistence()->loadPendingInvitesByRecipientEmail($this);
    }

    public function loadPendingInvitesByGroupId()
    {
        return $this->getPersistence()->loadPendingInvitesByGroupId($this);
    }

    public function cancelInvite(): void
    {
        $this->setStatus(self::INVITE_STATUS_CANCELLED);
        $this->getPersistence()->updateInviteStatus($this);
    }

    public function rejectInvite(): void
    {
        $this->setStatus(self::INVITE_STATUS_REJECTED);
        $this->getPersistence()->updateInviteStatus($this);
    }

    public function acceptInvite(): self
    {
        $this->getPersistence()->acceptInvite($this);

        return $this;
    }

    public function rejectAllPendingsInvites(): self
    {
        $this->getPersistence()->rejectAllPendingsInvites($this);

        return $this;
    }

    public function invite(): void
    {
        $this
            ->setCreatedAt(now())
            ->setId(Helper::uuid())
            ->setStatus(self::INVITE_STATUS_PENDING)
            ->setRecipient($this->getGroup()->getUser())
            ->setSender($this->getGroup()->getOwnerUser())
        ;

        $this->getPersistence()->inviteTeammate($this);
    }

    public function checkIfUserHasPendingInvite(): self
    {
        if ($this->getPersistence()->hasPendingInvite($this)) {
            throw new Exception('Já existe um convite pendente para este usuário.');
        }

        return $this;
    }
}
