<?php

namespace domain\invite;

interface InvitePersistenceInterface
{
    public function inviteTeammate(Invite $invite): void;
}