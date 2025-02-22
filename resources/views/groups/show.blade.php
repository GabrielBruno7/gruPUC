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

        <div class="card-header text-center">
            <h3>{{ $group->name }}</h3>
        </div>
        <div class="card-body text-center">
        <h5 class="text-center">📝 Informações do Grupo</h5>
        <table class="table table-bordered w-75 mx-auto">
            <tbody>
                <tr>
                    <th class="text-center">Nome</th>
                    <td class="text-start">{{ $group->name }}</td>
                </tr>
                <tr>
                    <th class="text-center">Curso</th>
                    <td class="text-start">{{ $group->course->name }}</td>
                </tr>
                <tr>
                    <th class="text-center">Período</th>
                    <td class="text-start">{{ $group->period . 'º' }}</td>
                </tr>
                <tr>
                    <th class="text-center">Administrador</th>
                    <td class="text-start">{{ $group->owner->name }}</td>
                </tr>
                <tr>
                    <th class="text-center">Data de Criação</th>
                    <td class="text-start">{{ $group->created_at->format('d/m/Y') }}</td>
                </tr>
            </tbody>
        </table>
            <hr>

            <h5 class="text-center">👨‍👩‍👧‍👦 Membros do Grupo</h5>
            <ul class="list-group mx-auto w-75">
                @foreach ($group->users as $user)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            @if ($user->id === $group->owner_id)
                                <span class="me-1">👑</span> <!-- Ícone da coroa -->
                            @else
                                <span class="me-1">👤</span> <!-- Ícone de pessoa normal -->
                            @endif
                            {{ $user->name }} ({{ $user->email }})
                            
                            @if ($user->pivot->status === 'pending')
                                <span class="text-warning">(Pendente)</span>
                            @endif
                        </div>
                        
                        @if (Auth::id() === $group->owner_id && $user->pivot->status === 'pending')
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
            </ul>


            @if (Auth::id() !== $group->owner_id && !$group->users->contains(Auth::user()))
                <form action="{{ route('groups.request.join', $group->id) }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="btn btn-primary mx-auto">Pedir para Entrar</button>
                </form>
            @endif

            @if (Auth::id() === $group->owner_id)
                <form action="{{ route('groups.destroy', $group->id) }}" method="POST" class="mt-4">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger mx-auto">✖ Deletar Grupo</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
