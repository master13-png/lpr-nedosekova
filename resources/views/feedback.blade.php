@extends('layouts.app')

@section('content')

<div class="container mt-4">

    <h2 class="mb-4">Обратная связь</h2>

    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('feedback.send') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">
                Имя
            </label>

            <input
                type="text"
                class="form-control"
                id="name"
                name="name"
                value="{{ old('name') }}"
                required
            >

            @error('name')
                <div class="text-danger">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="message" class="form-label">
                Сообщение
            </label>

            <textarea
                class="form-control"
                id="message"
                name="message"
                rows="4"
                required
            >{{ old('message') }}</textarea>

            @error('message')
                <div class="text-danger">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">
            Отправить
        </button>
    </form>

</div>

@endsection
