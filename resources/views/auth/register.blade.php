@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="col-md-5">
        <div class="card shadow-lg rounded-4">
            <div class="card-header text-center bg-primary text-white fw-bold">
                {{ __('Cadastre-se') }}
            </div>

            <div class="card-body p-4">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">{{ __('Nome') }}</label>
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">{{ __('E-mail PUC') }}</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('Senha') }}</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password-confirm" class="form-label">{{ __('Confirmar senha') }}</label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                    </div>

                    <div class="mb-3">
                        <label for="course" class="form-label">{{ __('Curso') }}</label>
                        <select id="course" name="course_id" class="form-control" required>
                            <option value="">Selecione um curso</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" data-period="{{ $course->period }}">{{ $course->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="period" class="form-label">{{ __('Período') }}</label>
                        <select id="period" name="period" class="form-control" required>
                            <option value="">Selecione um curso primeiro</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        {{ __('Enviar') }}
                    </button>
                </form>
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