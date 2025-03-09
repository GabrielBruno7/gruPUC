@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="col-md-5">
        <div class="card shadow-lg rounded-4">
            <div class="card-header text-center bg-primary text-white fw-bold">
                <i class="fas fa-users me-2"></i> {{ __('Novo grupo') }}
            </div>

            <div class="card-body p-4">
                <form action="{{ route('groups.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">
                            <i class="fas fa-users me-2"></i> {{ __('Nome do grupo') }}
                        </label>
                        <input type="text" id="name" name="name" class="form-control" required />
                    </div>

                    <div class="mb-3">
                        <label for="course" class="form-label">
                            <i class="fas fa-book me-2"></i> {{ __('Curso') }}
                        </label>
                        <input type="text" id="course" class="form-control" value="{{ $course->name }}" disabled />
                        <input type="hidden" name="course_id" value="{{ $course->id }}">
                    </div>

                    <div class="mb-3">
                        <label for="period" class="form-label">
                            <i class="far fa-calendar-alt me-2"></i> {{ __('Período') }}
                        </label>
                        <input type="text" id="period" class="form-control" value="{{ $user->period }}º" disabled />
                        <input type="hidden" name="period" value="{{ $user->period }}">
                    </div>

                    <div class="mb-3">
                        <label for="member" class="form-label">
                            <i class="fas fa-user me-2"></i> {{ __('Integrante') }}
                        </label>
                        <input type="text" id="member" class="form-control" value="{{ $user->name }}" disabled />
                    </div>

                    <div id="members-list"></div>
                    <button type="button" id="add-member" class="btn btn-success w-100 mb-3">
                        <i class="fas fa-user-plus me-2"></i> {{ __('Convidar Integrante') }}
                    </button>

                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="fas fa-paper-plane me-2"></i> {{ __('Enviar') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addMemberButton = document.getElementById('add-member');
        const membersList = document.getElementById('members-list');
        let memberCount = 0;

        addMemberButton.addEventListener('click', function() {
            memberCount++;
            const memberField = document.createElement('div');
            memberField.classList.add('mb-3', 'd-flex', 'align-items-center');

            memberField.innerHTML = `
                <input type="email" name="members[]" class="form-control me-2" placeholder="Email do integrante" required>
                <button type="button" class="btn btn-danger remove-member">
                    <i class="fas fa-times"></i>
                </button>
            `;

            membersList.appendChild(memberField);

            memberField.querySelector('.remove-member').addEventListener('click', function() {
                membersList.removeChild(memberField);
            });
        });
    });
</script>
@endsection
