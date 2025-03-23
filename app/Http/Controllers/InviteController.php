<?php

namespace App\Http\Controllers;

use Exception;
use infra\UserDb;
use infra\GroupDb;
use infra\InviteDb;
use domain\user\User;
use domain\group\Group;
use domain\invite\Invite;
use Illuminate\Support\Facades\Auth;


class InviteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function acceptInvite(string $id)
    {
        try {
            $user = Auth::user();

            $invitation = (new Invite(new InviteDb()))
                ->setId($id)
                ->loadInviteById()
                ->checkIfAuthUserIsRealReceiver($user->email)
                ->acceptInvite()
                ->rejectAllPendingsInvites()
            ;

            $invitation
                ->getGroup()
                ->setUser($invitation->getRecipient())
                ->insertTeammate()
            ;

            return redirect()->route('groups.index')->with('success', 'Convite aceito com sucesso!');
        } catch (\Exception $e) {
            return redirect()->route('groups.index')->with('error', $e->getMessage());
        }
    }

    public function inviteCancel(string $id)
    {
        try {
            if (!$id) {
                throw new Exception('Convite não encontrado.');
            }

            (new Invite(new InviteDb()))
                ->setId($id)
                ->loadInviteById()
                ->cancelInvite()
            ;

            return redirect()->back()->with('success', 'Convite cancelado com sucesso.');
        } catch (\Exception $e) {
            return redirect()->route('groups.index')->with('error', $e->getMessage());
        }
    }

    public function inviteReject(string $id)
    {
        try {
            if (!$id) {
                throw new Exception('Convite não encontrado.');
            }

            (new Invite(new InviteDb()))
                ->setId($id)
                ->loadInviteById()
                ->rejectInvite()
            ;

            return redirect()->back()->with('success', 'Convite rejeitado com sucesso.');
        } catch (\Exception $e) {
            return redirect()->route('groups.index')->with('error', $e->getMessage());
        }
    }

    public function requestJoin(string $id)
    {
        try {
            $user = (new User(new UserDb()))
                ->setId(Auth::id())
                ->loadById()
            ;

            $group = (new Group(new GroupDb()))
                ->setId($id)
                ->loadGroupById()
            ;

            $group
                ->setUser($user)
                ->insertTeammate()
            ;

            return redirect()->route(
                'groups.show',
                $group->getId())->with(
                    'success',
                    'Solicitação enviada para o administrador do grupo.'
                )
            ;
        } catch (Exception $e) {
            return redirect()->route('groups.index')->with('error', $e->getMessage());
        }
    }
}
