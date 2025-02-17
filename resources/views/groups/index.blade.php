@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Grupos Criados</h1>

    <!-- Exibe uma mensagem caso não existam grupos -->
    @if($groups->isEmpty())
        <p>Não há grupos criados ainda.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Curso</th>
                    <th>Criador</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groups as $group)
                    <tr>
                        <td>{{ $group->name }}</td>
                        <td>{{ $group->course }}</td>
                        <td>{{ $group->owner->name }}</td> <!-- Exibe o nome do criador -->
                        <td>
                            <!-- Adicione botões ou links para editar/exibir mais detalhes -->
                            <a href="{{ route('groups.show', $group->id) }}" class="btn btn-info">Ver Detalhes</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <a href="{{ route('groups.create') }}" class="btn btn-primary">Criar Novo Grupo</a>
</div>
@endsection
