<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class GroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $groups = Group::with(['course', 'owner'])
            ->where('status', 'ativo')
            ->whereNull('deleted_at')
            ->get();
    
        return view('groups.index', compact('groups'));
    }

    public function create()
    {
        $user = Auth::user();
        
        $hasGroup = DB::table('groups')
            ->where('owner_id', $user->id)
            ->where('status', 'ativo')
            ->whereNull('deleted_at')
            ->exists();

        if ($hasGroup) {
            return back()->withErrors(['error' => 'Não foi possível criar um novo grupo, você já possui um grupo ativo']);
        }

        $course = Course::where('id', $user['course_id'])->first();

        return view('groups.create', compact('user', 'course'));
    }

    public function show(string $id)
    {
        $group = Group::with('course', 'owner', 'users')->findOrFail($id);

        return view('groups.show', compact('group'));
    }

    public function destroy($id)
    {
        $group = Group::findOrFail($id);

        $hasActiveMembers = DB::table('user_group')
            ->join('groups', 'user_group.group_id', '=', 'groups.id')
            ->where('groups.status', 'ativo')
            ->whereIn('user_group.status', ['pending', 'approved']) // Correção
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
        ]);

        $group = Group::create([
            'owner_id' => Auth::id(),
            'name' => $groupData['name'],
            'period' => $groupData['period'],
            'course_id' => $groupData['course_id'],
        ]);

        $group->users()->attach(Auth::id(), [
            'uuid' => Uuid::uuid4()->toString(),
            'status' => 'approved',
        ]);

        return redirect()->route('groups.index');
    }

    public function requestJoin($groupId)
    {
        $group = Group::findOrFail($groupId);
    
        $group->users()->attach(Auth::id(), [
            'id' => Uuid::uuid4()->toString(),
            'status' => 'pending'
        ]);
    
        return redirect()->route('groups.show', $groupId)->with('success', 'Solicitação enviada para o dono do grupo.');
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
}
