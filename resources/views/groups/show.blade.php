@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center">
    <div class="card w-75"> <!-- Ajusta a largura do card -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

        <div class="card-header d-flex justify-content-center align-items-center position-relative">
            <a href="{{ route('groups.index') }}" class="btn btn-secondary ms-2 position-absolute start-0">
                ← Voltar
            </a>
            <h3 class="m-0">{{ $group->getName() }}</h3>
        </div>
        <div class="card-body text-center">
        <h5 class="text-center">📝 Informações do Grupo</h5>
        <table class="table table-bordered w-75 mx-auto">
            <tbody>
                <tr>
                    <th class="text-center">Nome</th>
                    <td class="text-start">{{ $group->getName() }}</td>
                </tr>
                <tr>
                    <th class="text-center">Curso</th>
                    <td class="text-start">{{ $group->getUser()->getCourse()->getName() }}</td>
                </tr>
                <tr>
                    <th class="text-center">Período</th>
                    <td class="text-start">{{ $group->getPeriod() . 'º' }}</td>
                </tr>
                <tr>
                    <th class="text-center">Administrador</th>
                    <td class="text-start">{{ $group->getOwnerUser()->getName() }}</td>
                </tr>
                <tr>
                    <th class="text-center">Data de Criação</th>
                    <td class="text-start">{{ $group->getCreatedAt()->format('d/m/Y') }}</td>
                </tr>
            </tbody>
        </table>
            <hr>
            <h5 class="text-center">👨‍👩‍👧‍👦 Membros do Grupo</h5>
            <ul class="list-group mx-auto w-75">
                @foreach ($group->getTeammates() as $user)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            @if ($user->id === $group->getOwnerUser()->getId())
                                <span class="me-1">👑</span>
                            @else
                                <span class="me-1">👤</span>
                            @endif
                            {{ $user->name }} ({{ $user->email }})
                            
                            @if ($user->status === 'pending')
                                <span class="text-warning">(Pendente)</span>
                            @endif
                        </div>

                        @if (Auth::id() === $group->getOwnerUser()->getId() && $user->status === 'pending')
                            <div>
                                <form action="{{ route('groups.approve', [$group->id, $user->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-success btn-sm">✔</button>
                                </form>
                                <form action="{{ route('groups.reject', [$group->id, $user->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-danger btn-sm">✖</button>
                                </form>
                            </div>
                        @endif
                    </li>
                @endforeach
                @if (Auth::id() === $group->getOwnerUser()->getId())
                    @foreach ($pendingInvitations as $invitation)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="me-1">📩</span>
                                {{ $invitation->email }} <span class="text-info">(Convite Enviado)</span>
                            </div>
                            <form action="{{ route('groups.cancel.invite') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $invitation->id }}">
                                <input type="hidden" name="group_id" value="{{ $invitation->group_id }}">
                                <button type="submit" class="btn btn-danger mx-auto">✖ Cancelar convite</button>
                            </form>
                        </li>
                    @endforeach
                @endif
            </ul>

            @if (Auth::id() !== $group->getOwnerUser()->getId() && !$group->users->contains(Auth::user()))
                <form action="{{ route('groups.request.join', $group->id) }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="btn btn-primary mx-auto">Pedir para Entrar</button>
                </form>
            @endif
            
            @if (Auth::id() === $group->getOwnerUser()->getId())
            <div class="modal fade" id="inviteModal" tabindex="-1" aria-labelledby="inviteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="inviteModalLabel">Convidar Usuário para o Grupo</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('groups.invite', $group->getId()) }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <label for="email" class="form-label">E-mail do Usuário:</label>
                                <input type="email" name="email" id="email" class="form-control" required placeholder="Digite o e-mail do usuário">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-success">Enviar Convite</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        @if (Auth::id() === $group->getOwnerUser()->getId())
            <div class="d-flex justify-content-center gap-2 mt-4">
                <button type="button" class="btn btn-info text-white" data-bs-toggle="modal" data-bs-target="#inviteModal">
                    ➕ Convidar Usuário
                </button>

                <form action="{{ route('groups.destroy', $group->getId()) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">✖ Deletar Grupo</button>
                </form>
            </div>
        @endif
        </div>
    </div>
</div>
@endsection
