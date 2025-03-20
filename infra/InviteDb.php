<?php

namespace infra;

use App\Models\Invitation;
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

    public function hasPendingInvite(Invite $invite): bool
    {
        return DB::table('invitations')
            ->where('email', $invite->getRecipient()->getEmail())
            ->where('status', Invite::INVITE_STATUS_PENDING)
            ->exists()
        ;
    }

    public function loadPendingInvitesByRecipientEmail(Invite $invite)
    {
        return  DB::table('invitations as i')
        ->join('groups as g', 'i.group_id', '=', 'g.id')
        ->join('users as u', 'i.invited_by', '=', 'u.id')
        ->select(
            'i.*',
            'g.id as group_id', 'g.name as group_name', // Campos do grupo
            'u.id as sender_id', 'u.name as sender_name' // Campos do usuário que enviou o convite
        )
        ->where('i.email', $invite->getRecipient()->getEmail())
        ->where('i.status', 'pending')
        ->get();

    }
}
