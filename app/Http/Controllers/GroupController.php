<?php

namespace App\Http\Controllers;

use Exception;

use infra\UserDb;
use infra\GroupDb;
use infra\InviteDb;
use infra\CourseDb;
use domain\user\User;
use domain\group\Group;
use domain\course\Course;
use domain\invite\Invite;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class GroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        try {
            $authUser = Auth::user();

            $course = (new Course(new CourseDb()))
                ->setId($authUser['course_id'])
                ->loadById()
            ;

            $user = (new User(new UserDb()))
                ->setId($authUser['id'])
                ->setName($authUser['name'])
                ->setRole($authUser['role'])
                ->setEmail($authUser['email'])
                ->setPeriod($authUser['period'])
                ->setCourse($course)
            ;

            $groups = (new Group(new GroupDb()))
                ->setUser($user)
                ->loadAllGroups()
            ;

            $pendingInvitations = (new Invite(new InviteDb()))
                ->setRecipient($user)
                ->loadPendingInvitesByRecipientEmail()
            ;

            $userHasGroup = $user->isUserAlreadyHaveAGroup();

            return view('groups.index', compact('groups', 'pendingInvitations', 'userHasGroup', 'user'));
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function create()
    {
        try {
            $authUser = Auth::user();

            $user = (new User(new UserDb()))
                ->setId($authUser['id'])
                ->loadById()
            ;

            $user->checkIfUserGroupFounder();

            return view('groups.create', compact('user'));
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function show(string $id)
    {
        try {
            $authUser = Auth::user();

            $user = (new User(new UserDb()))
                ->setId($authUser['id'])
                ->loadById()
            ;

            $group = (new Group(new GroupDb()))
                ->setId($id)
                ->setUser($user)
                ->loadGroupById()
            ;

            $pendingInvitations = (new Invite(new InviteDb()))
                ->setGroup($group)
                ->setRecipient($user)
                ->loadPendingInvitesByGroupId()
            ;

            return view('groups.show', compact('group', 'pendingInvitations'));
        } catch (Exception $e) {
            return redirect()->route('groups.index')->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $group = Group::findOrFail($id);

        $hasActiveMembers = DB::table('user_group')
            ->join('groups', 'user_group.group_id', '=', 'groups.id')
            ->where('groups.status', 'ativo')
            ->where('groups.id', $group->id)
            ->whereIn('user_group.status', ['pending', 'approved'])
            ->where('user_group.user_id', '!=', $group->owner_id)
            ->whereNull('groups.deleted_at')
            ->exists()
        ;

        if ($hasActiveMembers) {
            return redirect()->route('groups.show', $id)->with('error', 'Este grupo ainda possui membros ativos e não pode ser desativado.');
        }

        if (Auth::id() === $group->owner_id) {
            if ($group->status === 'inativo') {
                return redirect()->route('groups.index')->with('error', 'Este grupo já está desativado.');
            }

            $group->status = 'inativo';
            $group->deleted_at = now();
            $group->save();

            return redirect()->route('groups.index')->with('success', 'Grupo desativado com sucesso!');
        }

        return redirect()->route('groups.index')->with('error', 'Você não tem permissão para desativar este grupo.');
    }

    public function store(Request $request)
    {
        try {
            $groupData = $request->validate([
                'name' => 'required|string|max:255',
                'course_id' => 'required|uuid|exists:courses,id',
                'period' => 'required|integer',
                'members' => 'nullable|array',
                'members.*' => 'email|distinct',
            ]);

            $owner = (new User(new UserDb()))
                ->setId(Auth::id())
                ->loadById()
            ;

            $group = (new Group(new GroupDb()))
                ->setUser(new User(new UserDb()))
                ->setOwnerUser($owner)
                ->setName($groupData['name'])
                ->setPeriod($groupData['period'])
                ->setTeammates($groupData['members'] ?? null)
                ->create()
            ;

            (new Invite(new InviteDb()))
                ->setGroup($group)
                ->inviteTeammates()
            ;

            return redirect()->route('groups.show', $group->getId())->with('success', 'Novo grupo criado com sucesso!');
        } catch (Exception $e) {
            return redirect()->route('groups.index')->with('error', $e->getMessage());
        }
    }

    public function approveRequest(string $groupId, string $userId)
    {
        try {
            $user = (new User(new UserDb()))
                ->setId($userId)
                ->loadById()
            ;

            $group = (new Group(new GroupDb()))
                ->setId($groupId)
                ->loadGroupById()
            ;

            $group
                ->setUser($user)
                ->approveRequest()
            ;

            return redirect()->route('groups.show', $groupId)->with('success', 'Membro aprovado com sucesso!');
        } catch (Exception $e) {
            return redirect()->route('groups.index')->with('error', "Ocorreu um erro, entre em contato com o suporte.");
        }
    }

    public function rejectRequest(string $groupId, string $userId)
    {
        try {
            $user = (new User(new UserDb()))
                ->setId($userId)
                ->loadById()
            ;

            $group = (new Group(new GroupDb()))
                ->setId($groupId)
                ->loadGroupById()
                ->setUser($user)
                ->rejectRequest()
            ;

            return redirect()->route('groups.show', $groupId)->with('success', 'Membro rejeitado com sucesso!');
        } catch (Exception $e) {
            return redirect()->route('groups.index')->with('error', $e->getMessage());
        }
    }

    public function rejectt($id)
    {
        dd("f");
        $invitation = Invitation::findOrFail($id);

        if ($invitation->email !== Auth::user()->email) {
            return redirect()->route('groups.index')->with('error', 'Convite inválido.');
        }

        $invitation->status = 'rejected';
        $invitation->save();

        return redirect()->route('groups.index')->with('success', 'Convite recusado com sucesso!');
    }

    public function inviteTeammate(Request $request, string $groupId)
    {
        try {
            if (!$request->email) {
                return redirect()->back()->with('error', 'Nenhum e-mail enviado');
            }

            $user = (new User(new UserDb()))
                ->setEmail($request->email)
                ->loadByEmail()
            ;

            if (!$user) {
                return redirect()->back()->with('error',
                 'Nenhum usuário encontrado com este endereço de e-mail'
                );
            }

            $group = (new Group(new GroupDb()))
                ->setId($groupId)
                ->loadGroupById()
            ;

            $group->checkIfUserIsGroupOwnerToSendInvite(Auth::id());
            $group->setUser($user)->checkIfUserAlreadyInGroup();

            $invite = (new Invite(new InviteDb()))
                ->setGroup($group)
                ->setRecipient($user)
                ->checkIfUserHasPendingInvite()
            ;

            $invite->invite();

            return redirect()->back()->with('success', 'Convite enviado com sucesso!');
        } catch (Exception $e) {
            return redirect()->route('groups.index')->with('error', $e->getMessage());
        }
    }
}
