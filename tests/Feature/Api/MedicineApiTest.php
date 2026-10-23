<?php

namespace Tests\Feature\Api;

use App\Models\Manufacturer;
use App\Models\Medicine;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class MedicineApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_ping_returns_format_b(): void
    {
        $this->getJson(route('api.ping'))
            ->assertOk()
            ->assertJsonStructure([
                'status',
                'result' => ['message', 'time'],
                'student',
                'lesson',
            ])
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('result.message', 'pong')
            ->assertJsonPath('student', 'nedosekova')
            ->assertJsonPath('lesson', 'F1-FUTI, J8-LAIP, X6-CTVR');
    }

    public function test_index_returns_camel_case_resources_and_pagination(): void
    {
        $manufacturer = Manufacturer::factory()->create();

        for ($index = 1; $index <= 6; $index++) {
            Medicine::factory()->for($manufacturer)->create([
                'code' => sprintf('AB-%04d', $index),
                'name' => "Препарат {$index}",
                'price' => $index * 100,
            ]);
        }

        $this->getJson(route('api.medicines.index', [
            'per_page' => 2,
            'sort' => '-price',
        ]))
            ->assertOk()
            ->assertJsonStructure([
                'status',
                'result' => [[
                    'id',
                    'manufacturerId',
                    'code',
                    'name',
                    'form',
                    'dosageMg',
                    'price',
                    'byPrescription',
                    'manufacturer' => ['id', 'name', 'country'],
                    'createdAt',
                    'updatedAt',
                ]],
                'student',
                'lesson',
                'pagination' => ['page', 'pages', 'limit', 'count'],
            ])
            ->assertJsonPath('pagination.limit', 2)
            ->assertJsonPath('pagination.count', 6)
            ->assertJsonPath('result.0.code', 'AB-0006')
            ->assertJsonPath('result.0.price', 600);
    }

    public function test_store_creates_medicine_and_returns_201_location(): void
    {
        $manufacturer = Manufacturer::factory()->create();
        $data = $this->validMedicineData($manufacturer, 'QA-7101');

        $response = $this->postJson(route('api.medicines.store'), $data)
            ->assertCreated()
            ->assertJsonStructure([
                'status',
                'result' => [
                    'id',
                    'manufacturerId',
                    'code',
                    'name',
                    'form',
                    'dosageMg',
                    'price',
                    'byPrescription',
                    'manufacturer' => ['id', 'name', 'country'],
                    'createdAt',
                    'updatedAt',
                ],
                'student',
                'lesson',
            ])
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('result.manufacturerId', $manufacturer->id)
            ->assertJsonPath('result.code', 'QA-7101');

        $medicineId = $response->json('result.id');

        $response->assertHeader('Location', route('api.medicines.show', $medicineId));
        $this->assertDatabaseHas('medicines', [
            'id' => $medicineId,
            'manufacturer_id' => $manufacturer->id,
            'code' => 'QA-7101',
            'name' => 'Новый препарат',
        ]);
    }

    public function test_empty_store_returns_422_in_format_b(): void
    {
        $this->postJson(route('api.medicines.store'), [])
            ->assertUnprocessable()
            ->assertJsonStructure([
                'status',
                'httpCode',
                'message',
                'details' => ['manufacturerId', 'code', 'name', 'form', 'dosageMg', 'price'],
            ])
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('httpCode', 422)
            ->assertJsonPath('message', 'Ошибка валидации данных')
            ->assertJsonPath('details.name.0', 'Поле «Название» обязательно.');

        $this->assertDatabaseCount('medicines', 0);
    }

    public function test_malformed_json_body_returns_422_in_format_b(): void
    {
        $response = $this->call(
            'POST',
            route('api.medicines.store'),
            [],
            [],
            [],
            [
                'HTTP_ACCEPT' => 'application/json',
                'CONTENT_TYPE' => 'application/json',
            ],
            '{"name":'
        );

        $response->assertUnprocessable()
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('httpCode', 422)
            ->assertJsonPath('details.name.0', 'Поле «Название» обязательно.');

        $this->assertDatabaseCount('medicines', 0);
    }

    public function test_store_rejects_short_name_with_format_b_error(): void
    {
        $manufacturer = Manufacturer::factory()->create();
        $data = $this->validMedicineData($manufacturer, 'QA-7106');
        $data['name'] = 'Нос';

        $this->postJson(route('api.medicines.store'), $data)
            ->assertUnprocessable()
            ->assertJsonPath('details.name.0', 'Название должно содержать не менее 4 символов.');

        $this->assertDatabaseCount('medicines', 0);
    }

    public function test_store_rejects_nonexistent_manufacturer_with_camel_case_error(): void
    {
        $data = $this->validMedicineData(Manufacturer::factory()->create(), 'QA-7107');
        $data['manufacturerId'] = PHP_INT_MAX;

        $this->postJson(route('api.medicines.store'), $data)
            ->assertUnprocessable()
            ->assertJsonPath(
                'details.manufacturerId.0',
                'Выбранный производитель не существует.'
            );

        $this->assertDatabaseCount('medicines', 0);
    }

    public function test_store_rejects_invalid_code_format(): void
    {
        $data = $this->validMedicineData(Manufacturer::factory()->create(), 'QA-7108');
        $data['code'] = 'md-01';

        $this->postJson(route('api.medicines.store'), $data)
            ->assertUnprocessable()
            ->assertJsonPath(
                'details.code.0',
                'Код должен иметь формат AA-0000 (две заглавные латинские буквы и четыре цифры).'
            );

        $this->assertDatabaseCount('medicines', 0);
    }

    public function test_store_rejects_duplicate_code(): void
    {
        $manufacturer = Manufacturer::factory()->create();
        Medicine::factory()->for($manufacturer)->create(['code' => 'QA-7109']);

        $this->postJson(
            route('api.medicines.store'),
            $this->validMedicineData($manufacturer, 'QA-7109')
        )
            ->assertUnprocessable()
            ->assertJsonPath('details.code.0', 'Такой код препарата уже используется.');

        $this->assertDatabaseCount('medicines', 1);
    }

    public function test_show_returns_medicine_in_format_b(): void
    {
        $medicine = Medicine::factory()->create(['code' => 'QA-7102']);

        $this->getJson(route('api.medicines.show', $medicine))
            ->assertOk()
            ->assertJsonStructure([
                'status',
                'result' => ['id', 'manufacturerId', 'code', 'name', 'dosageMg', 'manufacturer'],
                'student',
                'lesson',
            ])
            ->assertJsonPath('result.id', $medicine->id)
            ->assertJsonPath('result.code', 'QA-7102')
            ->assertJsonPath('result.manufacturer.id', $medicine->manufacturer_id);
    }

    public function test_patch_updates_only_the_supplied_field(): void
    {
        $medicine = Medicine::factory()->create(['code' => 'QA-7103']);

        $this->patchJson(route('api.medicines.update', $medicine), [
            'name' => 'Обновлённый препарат',
        ])
            ->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('result.name', 'Обновлённый препарат');

        $this->assertDatabaseHas('medicines', [
            'id' => $medicine->id,
            'name' => 'Обновлённый препарат',
            'code' => 'QA-7103',
        ]);
    }

    public function test_put_with_one_field_returns_422_without_updating(): void
    {
        $medicine = Medicine::factory()->create([
            'code' => 'QA-7104',
            'name' => 'Исходный препарат',
        ]);

        $this->putJson(route('api.medicines.update', $medicine), [
            'name' => 'Новое название',
        ])
            ->assertUnprocessable()
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('httpCode', 422)
            ->assertJsonStructure([
                'details' => ['manufacturerId', 'code', 'form', 'dosageMg', 'price'],
            ]);

        $this->assertDatabaseHas('medicines', [
            'id' => $medicine->id,
            'name' => 'Исходный препарат',
        ]);
    }

    public function test_destroy_returns_204_and_deletes_medicine(): void
    {
        $medicine = Medicine::factory()->create(['code' => 'QA-7105']);

        $this->deleteJson(route('api.medicines.destroy', $medicine))
            ->assertNoContent();

        $this->assertModelMissing($medicine);
    }

    public function test_missing_medicine_returns_404_in_format_b(): void
    {
        $this->getJson(route('api.medicines.show', 999999))
            ->assertNotFound()
            ->assertExactJson([
                'status' => 'error',
                'httpCode' => 404,
                'message' => 'Запись не найдена',
                'details' => [],
            ]);
    }

    public function test_unsupported_api_method_returns_405_in_format_b(): void
    {
        $this->postJson(route('api.ping'), [])
            ->assertMethodNotAllowed()
            ->assertExactJson([
                'status' => 'error',
                'httpCode' => 405,
                'message' => 'Метод не поддерживается для этого адреса',
                'details' => [],
            ]);
    }

    /**
     * @return array<string, int|string|bool>
     */
    private function validMedicineData(Manufacturer $manufacturer, string $code): array
    {
        return [
            'manufacturerId' => $manufacturer->id,
            'code' => $code,
            'name' => 'Новый препарат',
            'form' => 'Таблетки',
            'dosageMg' => 250,
            'price' => '1250.00',
            'byPrescription' => false,
        ];
    }
}
