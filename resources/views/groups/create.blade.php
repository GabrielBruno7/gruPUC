@extends('layouts.app')

@section('content')
<section class="vh-100 bg-image">
  <div class="mask d-flex align-items-center h-100 gradient-custom-3">
    <div class="container h-100">
      <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-12 col-md-9 col-lg-7 col-xl-6">
          <div class="card" style="border-radius: 15px;">
            <div class="card-body p-5">
              <h2 class="text-uppercase text-center mb-5">Criar um novo grupo</h2>

              <form action="{{ route('groups.store') }}" method="POST">
                @csrf

                <div data-mdb-input-init class="form-outline mb-4">
                    <label class="form-label" for="name">Nome</label>
                    <input type="text" id="name" name="name" class="form-control form-control-lg" required />
                </div>

                <div class="mb-4">
                    <label class="form-label" for="course">Curso</label>
                    <select id="course" name="course" class="form-select form-control-lg" required>
                        <option value="">Selecione um curso</option>
                        @foreach ($courses as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex justify-content-center">
                  <button type="submit" class="btn btn-success btn-block btn-lg gradient-custom-4 text-body "> Criar grupo</button>
                </div>
              </form>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
