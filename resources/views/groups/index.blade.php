@extends('layouts.app')

@section('content')
<div class="container">
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

    @if($pendingInvitations->isNotEmpty())
        <div class="card border-warning mb-4">
            <div class="card-body d-flex align-items-center">
                <i class="fas fa-envelope text-warning me-3" style="font-size: 1.5rem;"></i>
                <div class="flex-grow-1">
                    <strong>Você tem {{ $pendingInvitations->count() }} convite(s) pendente(s).</strong>
                    <br>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#invitationModal" class="fw-bold">
                        Clique aqui para visualizar.
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-users me-2"></i> Grupos</h1>
        <a href="{{ route('groups.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i> Criar Novo Grupo
        </a>
    </div>

    @if(!$groups)
        <p><i class="fas fa-info-circle me-2"></i> Não há grupos disponíveis.</p>
    @else
        <div class="mt-4">
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th><i class="fas fa-users me-2"></i> Nome do grupo</th>
                        <th><i class="fas fa-book me-2"></i> Curso</th>
                        <th><i class="far fa-calendar-alt me-2"></i> Período</th>
                        <th><i class="fas fa-user me-2"></i> Criador</th>
                        <th><i class="fas fa-cogs me-2"></i> Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groups as $group)
                        <tr>
                            <td>{{ $group->name }}</td>
                            <td>{{ $group->course_name }}</td>
                            <td>{{ $group->period . 'º período' }}</td>
                            <td>{{ $group->owner_name ?? 'Desconhecido' }}</td>
                            <td>
                                <a href="{{ route('groups.show', $group->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye me-1"></i> Detalhes
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div class="modal fade" id="invitationModal" tabindex="-1" aria-labelledby="invitationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invitationModalLabel">
                    <i class="fas fa-envelope me-2"></i> Convites Pendentes
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($pendingInvitations->isEmpty())
                    <p><i class="fas fa-info-circle me-2"></i> Você não tem convites pendentes.</p>
                @else
                    <ul class="list-group">
                        @foreach($pendingInvitations as $invitation)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $invitation->group_name }}</strong><br>
                                    <small>Enviado por: {{ $invitation->sender_name }}</small>
                                </div>
                                <div>
                                    <form action="{{ route('invite.acceptInvite', $invitation->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-check"></i> Aceitar
                                        </button>
                                    </form>
                                    <form action="{{ route('invite.inviteReject', $invitation->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-times"></i> Recusar
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
