<?php

namespace infra;

use domain\group\Group;
use domain\invite\Invite;
use Illuminate\Support\Facades\DB;
use domain\invite\InvitePersistenceInterface;
use domain\user\User;
use Exception;

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
            ->where('group_id', $invite->getGroup()->getId())
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
                'g.id as group_id', 'g.name as group_name',
                'u.id as sender_id', 'u.name as sender_name'
            )
            ->where('i.email', $invite->getRecipient()->getEmail())
            ->where('i.status', 'pending')
            ->get()
        ;
    }

    public function loadPendingInvitesByGroupId(Invite $invite)
    {
        return  DB::table('invitations as i')
            ->where('i.group_id', $invite->getGroup()->getId())
            ->where('i.status', Invite::INVITE_STATUS_PENDING)
            ->get()
            ->toArray()
        ;
    }

    public function loadInviteById(Invite $invite): bool
    {
        $result = DB::table('invitations')
            ->where('id', $invite->getId())
            ->first()
        ;

        if (!$result) {
            return false;
        }

        $group = (new Group(new GroupDb()))->setId($result->group_id)->loadGroupById();
        $recipient = (new User(new UserDb()))->setEmail($result->email)->loadByEmail();
        $sender = (new User(new UserDb()))->setId($result->invited_by)->loadById();

        $invite
            ->setGroup($group)
            ->setSender($sender)
            ->setId($result->id)
            ->setRecipient($recipient)
            ->setStatus($result->status)
        ;

        return true;
    }

    public function updateInviteStatus(Invite $invite): void
    {
        $affectedRow = DB::table('invitations')
            ->where('id', $invite->getId())
            ->update(['status' => $invite->getStatus()])
        ;

        if (!$affectedRow) {
            throw new Exception("Houve um erro ao atualizar status do convite para '{$invite->getStatus()}'");
        }
    }

    public function rejectAllPendingsInvites(Invite $invite): void
    {
        $affectedRow = DB::table('invitations')
            ->where('email', $invite->getRecipient()->getEmail())
            ->where('status', Invite::INVITE_STATUS_PENDING)
            ->where('id', '!=', $invite->getId())
            ->update(['status' => Invite::INVITE_STATUS_CANCELLED])
        ;

        if (!$affectedRow) {
            throw new Exception('Houve um erro no cancelamento dos convite.');
        }
    }

    public function acceptInvite(Invite $invite): void
    {
        $affectedRow = DB::table('invitations')
            ->where('email', $invite->getRecipient()->getEmail())
            ->where('status', Invite::INVITE_STATUS_PENDING)
            ->where('id', $invite->getId())
            ->update(['status' => Invite::INVITE_STATUS_ACCEPTED])
        ;

        if (!$affectedRow) {
            throw new Exception('Houve um erro ao aceitar convite.');
        }
    }
}
