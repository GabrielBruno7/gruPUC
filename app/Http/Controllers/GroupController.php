<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Group;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Ramsey\Uuid\Uuid;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        $user = Auth::user();
        $course = Course::find($user['course_id']);
    
        $groups = Group::with(['course', 'owner'])
            ->where('status', 'ativo')
            ->whereNull('deleted_at')
            ->where('course_id', $user['course_id'])
            ->get();

        $groups = $groups->filter(fn($group) => $user->period === $group->period);


        $pendingInvitations = Invitation::with(['group', 'sender']) // <-- Carrega os relacionamentos
            ->where('email', $user->email)
            ->where('status', 'pending')
            ->get()
        ;
    
        return view('groups.index', compact('groups', 'pendingInvitations'));
    }

    public function create()
    {
        $user = Auth::user();
        
        $hasGroup = DB::table('groups')
            ->where('owner_id', $user->id)
            ->where('status', 'ativo')
            ->whereNull('deleted_at')
            ->exists()
        ;

        if ($hasGroup) {
            return back()->withErrors(['error' => 'Não foi possível criar um novo grupo, você já possui um grupo ativo']);
        }

        $course = Course::where('id', $user['course_id'])->first();

        return view('groups.create', compact('user', 'course'));
    }

    public function show(string $id)
    {
        $group = Group::with('course', 'owner', 'users')->findOrFail($id);

        $pendingInvitations = Invitation::where('group_id', $group->id)
            ->where('status', 'pending')
            ->get()
        ;

        return view('groups.show', compact('group', 'pendingInvitations'));
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
        $groupData = $request->validate([
            'name' => 'required|string|max:255',
            'course_id' => 'required|uuid|exists:courses,id',
            'period' => 'required|integer',
            'members' => 'nullable|array',
            'members.*' => 'email|distinct',
        ]);

        $group = Group::create([
            'owner_id' => Auth::id(),
            'name' => $groupData['name'],
            'period' => $groupData['period'],
            'course_id' => $groupData['course_id'],
        ]);
    
        $group->users()->attach(Auth::id(), [
            'id' => Uuid::uuid4()->toString(),
            'status' => 'approved',
        ]);
    

        if (!empty($groupData['members'])) {
            foreach ($groupData['members'] as $email) {

                $user = User::where('email', $email)->first();

                if (!$user) {
                    throw ValidationException::withMessages([
                        'members' => "O usuário com o email $email não foi encontrado.",
                    ]);
                }
        
                Invitation::create([
                    'group_id' => $group->id,
                    'invited_by' => Auth::id(),
                    'email' => $email,
                    'status' => 'pending',
                ]);
            }
        }

        return redirect()->route('groups.show', $group->id)->with('success', 'Novo grupo criado com sucesso!');
    }

    public function requestJoin($groupId)
    {
        $group = Group::findOrFail($groupId);
    
        $group->users()->attach(Auth::id(), [
            'id' => Uuid::uuid4()->toString(),
            'status' => 'pending'
        ]);
    
        return redirect()->route('groups.show', $groupId)->with('success', 'Solicitação enviada para o administrador do grupo.');
    }

    public function approve($groupId, $userId)
    {
        $group = Group::findOrFail($groupId);
        $user = User::findOrFail($userId);

        if (Auth::id() === $group->owner_id) {
            $group->users()->updateExistingPivot($user->id, ['status' => 'approved']);
            return redirect()->route('groups.show', $groupId)->with('success', 'Membro aprovado com sucesso!');
        }

        return redirect()->route('groups.show', $groupId)->with('error', 'Você não tem permissão para aprovar membros.');
    }

    public function reject($groupId, $userId)
    {
        $group = Group::findOrFail($groupId);
        $user = User::findOrFail($userId);

        if (Auth::id() === $group->owner_id) {
            $group->users()->detach($user->id);
            return redirect()->route('groups.show', $groupId)->with('success', 'Membro rejeitado e removido do grupo.');
        }

        return redirect()->route('groups.show', $groupId)->with('error', 'Você não tem permissão para rejeitar membros.');
    }

    public function accept($id)
    {
        $invitation = Invitation::findOrFail($id);
        $user = Auth::user();
    
        if ($invitation->email !== $user->email) {
            return redirect()->route('groups.index')->with('error', 'Convite inválido.');
        }
    
        DB::beginTransaction();
        try {
            // Adiciona o usuário ao grupo com status "approved"
            DB::table('user_group')->insert([
                'id'         => Uuid::uuid4()->toString(),
                'group_id'   => $invitation->group_id,
                'user_id'    => $user->id,
                'status'     => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    
            // Rejeita todos os outros convites pendentes do usuário
            Invitation::where('email', $user->email)
                ->where('status', 'pending')
                ->where('id', '!=', $invitation->id) // Exclui o convite que está sendo aceito
                ->update(['status' => 'rejected'])
            ;
    
            // Remove o convite aceito
            $invitation->delete();
    
            DB::commit();
            return redirect()->route('groups.index')->with('success', 'Convite aceito com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('groups.index')->with('error', 'Erro ao aceitar convite.');
        }
    }

    public function rejectt($id)
    {
        $invitation = Invitation::findOrFail($id);

        if ($invitation->email !== Auth::user()->email) {
            return redirect()->route('groups.index')->with('error', 'Convite inválido.');
        }

        $invitation->status = 'rejected';
        $invitation->save();

        return redirect()->route('groups.index')->with('success', 'Convite recusado com sucesso!');
    }

    public function invite(Request $request, $groupId)
    {
        $user = Auth::user();
        
        $isUser = User::where('email', $request->email)
            ->first()
        ;

        if (!$isUser) {
            return redirect()->back()->with('error', 'Nenhum usúario encontrado com este endereço de e-mail');
        }

        $group = Group::findOrFail($groupId);

        if (Auth::id() !== $group->owner_id) {
            return redirect()->back()->with('error', 'Apenas o administrador pode enviar convites.');
        }

        $isPendingInvite = Invitation::where('email', $request->email)
            ->where('status', 'pending')
            ->where('invited_by', $user->id)
            ->where('group_id', $group->id)
            ->first()
        ;

        if ($isPendingInvite) {
            return redirect()->back()->with('error', 'Já existe um convite pendente para este usuário.');
        }

        if ($group->users()->where('email', $request->email)->exists()) {
            return redirect()->back()->with('error', 'Usuário já faz parte do grupo.');
        }

        Invitation::create([
            'email' => $request->email,
            'invited_by' => $user->id,
            'group_id' => $groupId,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Convite enviado com sucesso!');
    }

    public function cancelInvite(Request $request)
    {
        $invitationId = $request['id'];

        $user = Auth::user();

        $isPendingInvite = Invitation::where('id', $invitationId)
            ->where('status', 'pending')
            ->where('invited_by', $user->id)
            ->where('group_id', $request['group_id'])
            ->exists()
        ;

        if (!$isPendingInvite) {
            return redirect()->back()->with('error', 'Convite inexistente!');
        }

        Invitation::where('id', $invitationId)
            ->update(['status' => 'cancelled'])
        ;

        return redirect()->back()->with('success', 'Convite cancelado com sucesso.');
    }
}
