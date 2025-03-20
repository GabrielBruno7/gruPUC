<?php

namespace infra;

use domain\invite\Invite;
use Illuminate\Support\Facades\DB;
use domain\invite\InvitePersistenceInterface;

class InviteDb implements InvitePersistenceInterface
{
    public function inviteTeammate(Invite $invite): void
    {
        DB::table('invitations')->insert([
            'id'         => $invite->getId(),
            'status'     => $invite->getStatus(),
            'created_at' => $invite->getCreatedAt(),
            'group_id'   => $invite->getGroup()->getId(),
            'invited_by' => $invite->getSender()->getId(),
            'email'      => $invite->getRecipient()->getEmail(),
        ]);
    }
}
