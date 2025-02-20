@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Cadastre-se') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Nome') }}</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('E-mail PUC') }}</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Senha') }}</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirmar senha') }}</label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

                        <div class="row mb-3">
                            <label for="course" class="col-md-4 col-form-label text-md-end">{{ __('Curso') }}</label>
                            <div class="col-md-6 position-relative">
                                <select id="course" name="course_id" class="form-control" required>
                                    <option value="">Selecione um curso</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" data-period="{{ $course->period }}">{{ $course->name }}</option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down position-absolute" style="top: 50%; right: 30px; transform: translateY(-50%);"></i>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="period" class="col-md-4 col-form-label text-md-end">{{ __('Período') }}</label>
                            <div class="col-md-6">
                                <select id="period" name="period" class="form-control" required>
                                    <option value="">Selecione um curso primeiro</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Enviar') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const courseSelect = document.getElementById("course");
        const periodSelect = document.getElementById("period");

        courseSelect.addEventListener("change", function() {
            const selectedOption = courseSelect.options[courseSelect.selectedIndex];
            const periodCount = selectedOption.getAttribute("data-period");

            periodSelect.innerHTML = '<option value="">Selecione o período</option>';

            if (periodCount) {
                for (let i = 1; i <= periodCount; i++) {
                    let option = document.createElement("option");
                    option.value = i;
                    option.textContent = `${i}º Período`;
                    periodSelect.appendChild(option);
                }
            }
        });
    });
</script>
@endsection
