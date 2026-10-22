<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    // Статические данные для ЛПР №8
    private function medicines(): array
    {
        return [
            [
                'id' => 1,
                'code' => 'MD-0001',
                'name' => 'Парацетамол',
                'manufacturer' => 'Борисовский ЗМП',
                'form' => 'Таблетки',
                'dosage_mg' => 500,
                'price' => 450.00,
                'by_prescription' => false,
            ],
            [
                'id' => 2,
                'code' => 'MD-0002',
                'name' => 'Ибупрофен',
                'manufacturer' => 'Борисовский ЗМП',
                'form' => 'Таблетки',
                'dosage_mg' => 200,
                'price' => 650.00,
                'by_prescription' => false,
            ],
            [
                'id' => 3,
                'code' => 'MD-0003',
                'name' => 'Амоксициллин',
                'manufacturer' => 'Sandoz',
                'form' => 'Капсулы',
                'dosage_mg' => 500,
                'price' => 1800.00,
                'by_prescription' => true,
            ],
            [
                'id' => 4,
                'code' => 'MD-0004',
                'name' => 'Цетиризин',
                'manufacturer' => 'Sandoz',
                'form' => 'Таблетки',
                'dosage_mg' => 10,
                'price' => 900.00,
                'by_prescription' => false,
            ],
            [
                'id' => 5,
                'code' => 'MD-0005',
                'name' => 'Омепразол',
                'manufacturer' => 'KRKA',
                'form' => 'Капсулы',
                'dosage_mg' => 20,
                'price' => 1100.00,
                'by_prescription' => false,
            ],
            [
                'id' => 6,
                'code' => 'MD-0006',
                'name' => 'Лоратадин',
                'manufacturer' => 'KRKA',
                'form' => 'Таблетки',
                'dosage_mg' => 10,
                'price' => 750.00,
                'by_prescription' => false,
            ],
            [
                'id' => 7,
                'code' => 'MD-0007',
                'name' => 'Метформин',
                'manufacturer' => 'Berlin-Chemie',
                'form' => 'Таблетки',
                'dosage_mg' => 500,
                'price' => 1350.00,
                'by_prescription' => true,
            ],
        ];
    }

    // GET /catalog
    public function index(Request $request)
    {
        $medicines = collect($this->medicines());

        $search = $request->query('q');

        if ($search) {
            $medicines = $medicines->filter(
                fn ($medicine) =>
                    mb_stripos($medicine['name'], $search) !== false
            );
        }

        $sort = $request->query('sort', 'name');

        if (in_array($sort, ['name', 'dosage_mg', 'price'])) {
            $medicines = $medicines->sortBy($sort);
        }

        return view('catalog.index', [
            'title' => 'Каталог лекарственных препаратов',
            'medicines' => $medicines,
            'search' => $search,
            'sort' => $sort,
        ]);
    }

    // GET /catalog/1
    public function show(int $id)
    {
        $medicine = collect($this->medicines())
            ->firstWhere('id', $id);

        abort_if(! $medicine, 404);

        return view('catalog.show', [
            'title' => $medicine['name'],
            'medicine' => $medicine,
        ]);
    }

    // GET /catalog/code/MD-0001
    public function byCode(string $code)
    {
        $medicine = collect($this->medicines())
            ->firstWhere('code', $code);

        abort_if(! $medicine, 404);

        return view('catalog.show', [
            'title' => $medicine['name'],
            'medicine' => $medicine,
        ]);
    }

    // GET /catalog/manufacturer/Sandoz
    public function byManufacturer(?string $manufacturer = null)
    {
        $medicines = collect($this->medicines());

        if ($manufacturer !== null) {
            $medicines = $medicines->where(
                'manufacturer',
                $manufacturer
            );
        }

        return view('catalog.index', [
            'title' => $manufacturer
                ? "Препараты производителя: {$manufacturer}"
                : 'Все производители',
            'medicines' => $medicines,
            'search' => null,
            'sort' => 'name',
        ]);
    }
}
