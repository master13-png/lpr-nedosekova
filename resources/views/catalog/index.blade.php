
@extends('layouts.app')

@section('content')

<h1 class="mb-4">{{ $title }}</h1>

<form method="GET"
      action="{{ route('catalog.index') }}"
      class="row g-2 mb-4">

    <div class="col-md-4">
        <input
            type="text"
            name="q"
            value="{{ $search }}"
            class="form-control"
            placeholder="Название препарата..."
        >
    </div>

    <div class="col-md-3">
        <select name="sort" class="form-select">

            <option value="latest"
                @selected($sort === 'latest')>
                Сначала новые
            </option>

            <option value="name"
                @selected($sort === 'name')>
                По названию
            </option>

            <option value="dosage_mg"
                @selected($sort === 'dosage_mg')>
                По дозировке
            </option>

            <option value="price"
                @selected($sort === 'price')>
                По цене
            </option>

        </select>
    </div>

    <div class="col-auto">
        <button class="btn btn-primary">
            Найти
        </button>
    </div>
</form>

<table class="table table-striped table-bordered">

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
        </tr>
    </thead>

    <tbody>
        @forelse ($medicines as $medicine)
            <tr>
                <td>{{ $medicines->firstItem() + $loop->index }}</td>

                <td>
                    <a href="{{ route('catalog.code', ['code' => $medicine->code]) }}">
                        {{ $medicine->code }}
                    </a>
                </td>

                <td>
                    <a href="{{ route('catalog.show', ['id' => $medicine->id]) }}">
                        {{ $medicine->name }}
                    </a>
                </td>

                <td>
                    <a href="{{ route('catalog.manufacturer', [
                        'manufacturer' => $medicine->manufacturer->name
                    ]) }}">
                        {{ $medicine->manufacturer->name }}
                    </a>
                </td>

                <td>{{ $medicine->form }}</td>

                <td>{{ $medicine->dosage_mg }} мг</td>

                <td>
                    {{ number_format((float) $medicine->price, 2, ',', ' ') }} ₸
                </td>

                <td>
                    @if ($medicine->by_prescription)
                        <span class="badge bg-warning text-dark">
                            По рецепту
                        </span>
                    @else
                        <span class="badge bg-success">
                            Без рецепта
                        </span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-muted">
                    Ничего не найдено.
                </td>
            </tr>
        @endforelse
    </tbody>

</table>

{{ $medicines->links() }}

@endsection
