@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card">

                <div class="card-header">
                    Вход
                </div>

                <div class="card-body">

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="login" class="form-label">
                                Логин
                            </label>

                            <input
                                id="login"
                                type="text"
                                class="form-control @error('login') is-invalid @enderror"
                                name="login"
                                value="{{ old('login') }}"
                                required
                                autofocus
                            >

                            @error('login')
                                <span class="invalid-feedback">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-outline-primary">
                            Войти
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>
</div>

@endsection