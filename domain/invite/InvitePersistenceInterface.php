<?php

namespace domain\invite;

interface InvitePersistenceInterface
{
    public function inviteTeammate(Invite $invite): void;
    public function hasPendingInvite(Invite $invite): bool;
    public function loadPendingInvitesByRecipientEmail(Invite $invite);
}