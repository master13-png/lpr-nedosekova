
@extends('layouts.app')

@section('content')

<h1 class="mb-4">{{ $medicine['name'] }}</h1>

<div class="card">
    <div class="card-body">

        <dl class="row">

            <dt class="col-sm-4">Код препарата</dt>
            <dd class="col-sm-8">{{ $medicine['code'] }}</dd>

            <dt class="col-sm-4">Название</dt>
            <dd class="col-sm-8">{{ $medicine['name'] }}</dd>

            <dt class="col-sm-4">Производитель</dt>
            <dd class="col-sm-8">
                {{ $medicine['manufacturer'] }}
            </dd>

            <dt class="col-sm-4">Форма выпуска</dt>
            <dd class="col-sm-8">{{ $medicine['form'] }}</dd>

            <dt class="col-sm-4">Дозировка</dt>
            <dd class="col-sm-8">
                {{ $medicine['dosage_mg'] }} мг
            </dd>

            <dt class="col-sm-4">Цена</dt>
            <dd class="col-sm-8">
                {{ number_format($medicine['price'], 2, ',', ' ') }} ₸
            </dd>

            <dt class="col-sm-4">Отпуск</dt>
            <dd class="col-sm-8">
                {{ $medicine['by_prescription']
                    ? 'По рецепту'
                    : 'Без рецепта' }}
            </dd>

        </dl>

        <a href="{{ route('catalog.index') }}"
           class="btn btn-secondary">
            ← Вернуться к каталогу
        </a>

    </div>
</div>

@endsection
