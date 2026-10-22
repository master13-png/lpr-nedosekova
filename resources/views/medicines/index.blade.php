@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">{{ $title }}</h1>
    <a href="{{ route('medicines.create') }}" class="btn btn-success">
        + Добавить препарат
    </a>
</div>

@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif

<form method="GET" action="{{ route('medicines.index') }}" class="row g-2 mb-4">
    <div class="col-md-4">
        <input
            type="search"
            name="q"
            value="{{ $search }}"
            class="form-control"
            placeholder="Название или код препарата..."
            aria-label="Поиск препарата"
        >
    </div>

    <div class="col-md-3">
        <select name="sort" class="form-select" aria-label="Сортировка">
            <option value="latest" @selected($sort === 'latest')>Сначала новые</option>
            <option value="name" @selected($sort === 'name')>По названию</option>
            <option value="dosage_mg" @selected($sort === 'dosage_mg')>По дозировке</option>
            <option value="price" @selected($sort === 'price')>По цене</option>
        </select>
    </div>

    <div class="col-auto">
        <button class="btn btn-primary">Найти</button>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-dark">
            <tr>
                <th>№</th>
                <th>Код</th>
                <th>Название</th>
                <th>Производитель</th>
                <th>Форма выпуска</th>
                <th>Дозировка</th>
                <th>Цена</th>
                <th>Рецепт</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($medicines as $medicine)
                <tr>
                    <td>{{ $medicines->firstItem() + $loop->index }}</td>
                    <td>{{ $medicine->code }}</td>
                    <td>{{ $medicine->name }}</td>
                    <td>{{ $medicine->manufacturer->name }}</td>
                    <td>{{ $medicine->form }}</td>
                    <td>{{ $medicine->dosage_mg }} мг</td>
                    <td>{{ number_format((float) $medicine->price, 2, ',', ' ') }} ₸</td>
                    <td>
                        @if ($medicine->by_prescription)
                            <span class="badge bg-warning text-dark">По рецепту</span>
                        @else
                            <span class="badge bg-success">Без рецепта</span>
                        @endif
                    </td>
                    <td class="text-nowrap text-end">
                        <a
                            href="{{ route('medicines.edit', $medicine) }}"
                            class="btn btn-sm btn-outline-primary"
                        >
                            Изменить
                        </a>
                        <form
                            method="POST"
                            action="{{ route('medicines.destroy', $medicine) }}"
                            class="d-inline"
                            onsubmit="return confirm('Удалить препарат?')"
                        >
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Удалить</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-muted">Ничего не найдено.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $medicines->links() }}

@endsection
