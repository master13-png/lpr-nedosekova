<?php

namespace App\Http\Controllers;

use App\Models\Manufacturer;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        return $this->listing($request);
    }

    public function show(int $id): View
    {
        $medicine = Medicine::with('manufacturer')->findOrFail($id);

        return view('catalog.show', [
            'title' => $medicine->name,
            'medicine' => $medicine,
        ]);
    }

    public function byCode(string $code): View
    {
        $medicine = Medicine::with('manufacturer')
            ->where('code', $code)
            ->firstOrFail();

        return view('catalog.show', [
            'title' => $medicine->name,
            'medicine' => $medicine,
        ]);
    }

    public function byManufacturer(Request $request, ?string $manufacturer = null): View
    {
        $selectedManufacturer = $manufacturer === null
            ? null
            : Manufacturer::where('name', $manufacturer)->firstOrFail();

        return $this->listing($request, $selectedManufacturer);
    }

    private function listing(Request $request, ?Manufacturer $manufacturer = null): View
    {
        $query = Medicine::with('manufacturer');

        if ($manufacturer !== null) {
            $query->whereBelongsTo($manufacturer);
        }

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

        return view('catalog.index', [
            'title' => $manufacturer === null
                ? 'Каталог лекарственных препаратов'
                : "Препараты производителя: {$manufacturer->name}",
            'medicines' => $query->paginate(5)->withQueryString(),
            'search' => $search,
            'sort' => $sort,
        ]);
    }
}
