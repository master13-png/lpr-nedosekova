@extends('layouts.app')

@section('content')

<h1 class="mb-4">{{ $title }}</h1>

<form
    method="POST"
    action="{{ $medicine->exists ? route('medicines.update', $medicine) : route('medicines.store') }}"
    class="col-lg-8"
>
    @csrf
    @if ($medicine->exists)
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="manufacturer_id" class="form-label">Производитель</label>
        <select
            id="manufacturer_id"
            name="manufacturer_id"
            class="form-select @error('manufacturer_id') is-invalid @enderror"
            required
        >
            <option value="">Выберите производителя</option>
            @foreach ($manufacturers as $manufacturer)
                <option
                    value="{{ $manufacturer->id }}"
                    @selected(old('manufacturer_id', $medicine->manufacturer_id) == $manufacturer->id)
                >
                    {{ $manufacturer->name }} ({{ $manufacturer->country }})
                </option>
            @endforeach
        </select>
        @error('manufacturer_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="code" class="form-label">Код препарата</label>
        <input
            id="code"
            name="code"
            value="{{ old('code', $medicine->code) }}"
            class="form-control @error('code') is-invalid @enderror"
            maxlength="7"
            pattern="[A-Z]{2}-[0-9]{4}"
            placeholder="MD-0001"
            required
        >
        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="name" class="form-label">Название</label>
        <input
            id="name"
            name="name"
            value="{{ old('name', $medicine->name) }}"
            class="form-control @error('name') is-invalid @enderror"
            minlength="4"
            maxlength="142"
            required
        >
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="form" class="form-label">Форма выпуска</label>
        <input
            id="form"
            name="form"
            value="{{ old('form', $medicine->form) }}"
            class="form-control @error('form') is-invalid @enderror"
            maxlength="50"
            required
        >
        @error('form')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="dosage_mg" class="form-label">Дозировка, мг</label>
        <input
            type="number"
            id="dosage_mg"
            name="dosage_mg"
            value="{{ old('dosage_mg', $medicine->dosage_mg) }}"
            class="form-control @error('dosage_mg') is-invalid @enderror"
            min="1"
            max="10000"
            required
        >
        @error('dosage_mg')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="price" class="form-label">Цена, ₸</label>
        <input
            type="number"
            id="price"
            name="price"
            value="{{ old('price', $medicine->price) }}"
            class="form-control @error('price') is-invalid @enderror"
            min="0.01"
            max="99999999.99"
            step="0.01"
            required
        >
        @error('price')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-check mb-3">
        <input
            type="checkbox"
            id="by_prescription"
            name="by_prescription"
            value="1"
            class="form-check-input @error('by_prescription') is-invalid @enderror"
            @checked(old('by_prescription', $medicine->by_prescription))
        >
        <label for="by_prescription" class="form-check-label">Отпускается по рецепту</label>
        @error('by_prescription')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button class="btn btn-primary">Сохранить</button>
    <a href="{{ route('medicines.index') }}" class="btn btn-secondary">Отмена</a>
</form>

@endsection
