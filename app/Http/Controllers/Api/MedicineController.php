<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MedicineApiRequest;
use App\Http\Resources\MedicineResource;
use App\Models\Medicine;
use App\Support\ApiFormat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class MedicineController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $sorts = [
            'name' => 'name',
            '-name' => 'name',
            'dosageMg' => 'dosage_mg',
            '-dosageMg' => 'dosage_mg',
            'price' => 'price',
            '-price' => 'price',
            'createdAt' => 'created_at',
            '-createdAt' => 'created_at',
        ];

        $request->validate([
            'q' => ['sometimes', 'string', 'max:'.config('lab.str_len')],
            'sort' => ['sometimes', 'string', Rule::in(array_keys($sorts))],
            'per_page' => ['sometimes', 'integer', 'between:1,50'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ]);

        $query = Medicine::with('manufacturer');
        $search = trim((string) $request->query('q', ''));

        if ($search !== '') {
            $query->where(function (Builder $query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $defaultSort = config('lab.sort') === 'created_at DESC' ? '-createdAt' : 'createdAt';
        $sort = $request->query('sort', $defaultSort);
        $query->orderBy($sorts[$sort], str_starts_with($sort, '-') ? 'desc' : 'asc');

        $medicines = $query->paginate($request->integer('per_page', config('lab.per_page')));
        $result = $medicines->getCollection()
            ->map(fn (Medicine $medicine): array => (new MedicineResource($medicine))->resolve($request))
            ->all();

        return ApiFormat::success($result, 200, [
            'pagination' => [
                'page' => $medicines->currentPage(),
                'pages' => $medicines->lastPage(),
                'limit' => $medicines->perPage(),
                'count' => $medicines->total(),
            ],
        ]);
    }

    public function store(MedicineApiRequest $request): JsonResponse
    {
        $medicine = Medicine::create($request->validated())->load('manufacturer');

        return ApiFormat::success(new MedicineResource($medicine)->resolve($request), 201)
            ->header('Location', route('api.medicines.show', $medicine));
    }

    public function show(Medicine $medicine, Request $request): JsonResponse
    {
        $medicine->load('manufacturer');

        return ApiFormat::success(new MedicineResource($medicine)->resolve($request));
    }

    public function update(MedicineApiRequest $request, Medicine $medicine): JsonResponse
    {
        $medicine->update($request->validated());
        $medicine->load('manufacturer');

        return ApiFormat::success(new MedicineResource($medicine)->resolve($request));
    }

    public function destroy(Medicine $medicine): Response
    {
        $medicine->delete();

        return response()->noContent();
    }
}
