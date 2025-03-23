<?php

namespace domain\invite;

interface InvitePersistenceInterface
{
    public function inviteTeammate(Invite $invite): void;
    public function hasPendingInvite(Invite $invite): bool;
    public function loadPendingInvitesByRecipientEmail(Invite $invite);
    public function loadPendingInvitesByGroupId(Invite $invite);
    public function updateInviteStatus(Invite $invite);
    public function rejectAllPendingsInvites(Invite $invite);
    public function acceptInvite(Invite $invite);
    public function loadInviteById(Invite $invite);
}