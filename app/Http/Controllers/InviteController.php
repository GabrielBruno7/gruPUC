<?php

namespace App\Http\Controllers;

use domain\group\Group;
use domain\invite\Invite;
use domain\user\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use infra\GroupDb;
use infra\InviteDb;
use infra\UserDb;

class InviteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function acceptInvite($id)
    {
        dd($id);
        // dd($id);
        // // $invitation = Invitation::findOrFail($id);
        // dd($invitation);
        // // $user = Auth::user();
    
        // if ($invitation->email !== $user->email) {
        //     return redirect()->route('groups.index')->with('error', 'Convite inválido.');
        // }
    
        // // DB::beginTransaction();
        // try {
        //     // Adiciona o usuário ao grupo com status "approved"
        //     DB::table('user_group')->insert([
        //         'id'         => Uuid::uuid4()->toString(),
        //         'group_id'   => $invitation->group_id,
        //         'user_id'    => $user->id,
        //         'status'     => 'approved',
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ]);
    
        //     // Rejeita todos os outros convites pendentes do usuário
        //     Invitation::where('email', $user->email)
        //         ->where('status', 'pending')
        //         ->where('id', '!=', $invitation->id) // Exclui o convite que está sendo aceito
        //         ->update(['status' => 'rejected'])
        //     ;
    
        //     // Remove o convite aceito
        //     $invitation->delete();
    
        //     DB::commit();
        //     return redirect()->route('groups.index')->with('success', 'Convite aceito com sucesso!');
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return redirect()->route('groups.index')->with('error', 'Erro ao aceitar convite.');
        // }
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
                ->insertPendingTeammate()
            ;

            return redirect()->route(
                'groups.show',
                $group->getId())->with('success', 'Solicitação enviada para o administrador do grupo.')
            ;
        } catch (Exception $e) {
            return redirect()->route('groups.index')->with('error', $e->getMessage());
        }
    }
}
