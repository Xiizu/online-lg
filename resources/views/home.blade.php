@extends('layouts.app')
@section('title', 'Connexion')
@section('body')
    <div class="card text-center mt-5 mx-auto" style="max-width: 400px;">
        <div class="card-header">
            <h2>Connexion</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input
                    type="password"
                    name="password"
                    placeholder="Mot de passe..."
                    class="form-input mb-3 w-100"
                    required
                    autofocus
                >
                <button
                    type="submit"
                    id="login-button"
                    class="btn btn-primary">
                        Entrer

                </button>
            </form>
        </div>
    </div>
    <script>
        document.getElementById('login-button').addEventListener('click', function() {
            this.disabled = true;
            this.innerText = 'Connexion en cours...';
            this.form.submit();
        });
    </script>
    @error('password')
        <div class="alert alert-danger alert-dismissible fade show mt-3 mx-auto" style="max-width: 400px;" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @enderror
    @error('login')
        <div class="alert alert-danger alert-dismissible fade show mt-3 mx-auto" style="max-width: 400px;" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @enderror
@endsection
