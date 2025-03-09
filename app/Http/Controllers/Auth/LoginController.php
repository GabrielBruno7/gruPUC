<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/groups/create';

    protected function redirectTo()
    {
        $user = Auth::user();

        // Verifica se o usuário já está em um grupo ativo
        $group = DB::table('user_group')
            ->join('groups', 'user_group.group_id', '=', 'groups.id')
            ->where('user_group.user_id', $user->id)
            ->where('groups.status', 'ativo')
            ->whereIn('user_group.status', ['pending', 'approved']) // Correção
            ->whereNull('groups.deleted_at')
            ->select('groups.id')
            ->first()
        ;

        // Se o usuário já estiver em um grupo ativo, redireciona para a página de detalhes desse grupo
        if ($group) {
            return route('groups.show', $group->id);
        }
        // Caso contrário, redireciona para a criação de um novo grupo
        return route('groups.index');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
}
