@extends('layouts.app')

@section('content')
<div class="container">
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Exibir mensagens de sucesso -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <h1>Grupos Disponíveis</h1>

    <!-- Exibe uma mensagem caso não existam grupos -->
    @if($groups->isEmpty())
        <p>Não há grupos disponíveis.</p>
    @else
        <table class="table table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Nome do grupo</th>
                    <th>Curso</th>
                    <th>Período</th>
                    <th>Criador</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groups as $group)
                    <tr>
                        <td>{{ $group->name }}</td>
                        <td>{{ $group->course?->name ?? 'Sem curso' }}</td>
                        <td>{{ $group->period . 'º período' }}</td>
                        <td>{{ $group->owner?->name ?? 'Desconhecido' }}</td>
                        <td>
                            <a href="{{ route('groups.show', $group->id) }}" class="btn btn-info btn-sm">Detalhes</a>
                      
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
    <a href="{{ route('groups.create') }}" class="btn btn-primary">Criar Novo Grupo</a>
</div>
@endsection
