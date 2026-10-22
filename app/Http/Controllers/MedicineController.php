<?php

namespace App\Http\Controllers;

use App\Http\Requests\MedicineRequest;
use App\Models\Manufacturer;
use App\Models\Medicine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MedicineController extends Controller
{
    public function index(Request $request): View
    {
        $query = Medicine::with('manufacturer');
        $search = $request->query('q');
        $search = is_string($search) ? trim($search) : '';

        if ($search !== '') {
            $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $requestedSort = $request->query('sort');
        $sort = is_string($requestedSort)
            && in_array($requestedSort, ['name', 'dosage_mg', 'price'], true)
                ? $requestedSort
                : 'latest';

        if ($sort === 'latest') {
            $query->latest();
        } else {
            $query->orderBy($sort);
        }

        return view('medicines.index', [
            'title' => 'Управление препаратами',
            'medicines' => $query->paginate(5)->withQueryString(),
            'search' => $search,
            'sort' => $sort,
        ]);
    }

    public function create(): View
    {
        return view('medicines.form', [
            'title' => 'Новый препарат',
            'medicine' => new Medicine,
            'manufacturers' => Manufacturer::orderBy('name')->get(),
        ]);
    }

    public function store(MedicineRequest $request): RedirectResponse
    {
        $medicine = Medicine::create($request->validated());

        return redirect()
            ->route('medicines.index')
            ->with('status', "Добавлено: {$medicine->name} (код {$medicine->code}).");
    }

    public function edit(Medicine $medicine): View
    {
        return view('medicines.form', [
            'title' => 'Редактирование препарата',
            'medicine' => $medicine,
            'manufacturers' => Manufacturer::orderBy('name')->get(),
        ]);
    }

    public function update(MedicineRequest $request, Medicine $medicine): RedirectResponse
    {
        $medicine->update($request->validated());

        return redirect()
            ->route('medicines.index')
            ->with('status', "Изменения сохранены: {$medicine->name}.");
    }

    public function destroy(Medicine $medicine): RedirectResponse
    {
        $name = $medicine->name;
        $medicine->delete();

        return redirect()
            ->route('medicines.index')
            ->with('status', "Удалено: {$name}.");
    }
}
