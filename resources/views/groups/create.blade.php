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
                    <label class="form-label" for="name">Nome do grupo</label>
                    <input type="text" id="name" name="name" class="form-control form-control-lg" required />
                </div>

                <div class="mb-4">
                    <label class="form-label" for="course">Curso</label>
                    <input type="text" id="course" class="form-control form-control-lg" value="{{ $course->name }}" disabled />
                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                </div>

                <div class="mb-4">
                    <label class="form-label" for="period">Período</label>
                    <input type="text" id="period" class="form-control form-control-lg" value="{{ $user->period }}" disabled />
                    <input type="hidden" name="period" value="{{ $user->period }}">
                </div>

                <div class="mb-4">
                    <label class="form-label" for="member">Integrante</label>
                    <input type="text" id="member" class="form-control form-control-lg" value="{{ $user->name }}" disabled />
                </div>
                
                <div class="d-flex justify-content-center">
                  <button type="submit" class="btn btn-success btn-block btn-lg gradient-custom-4 text-body ">Enviar</button>
                </div>
                
                <!-- <div class="d-flex justify-content-center">
                  <button type="submit" class="btn btn-success btn-block btn-lg gradient-custom-4 text-body ">Convidar ( em breve )</button>
                </div> -->
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
