<?php

namespace Tests\Feature;

use App\Models\Manufacturer;
use App\Models\Medicine;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class MedicineCrudTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_medicine_list_paginates_five_records_in_newest_order(): void
    {
        $manufacturer = Manufacturer::factory()->create();

        for ($index = 1; $index <= 6; $index++) {
            Medicine::factory()
                ->for($manufacturer)
                ->create([
                    'code' => sprintf('AB-%04d', $index),
                    'name' => "Препарат {$index}",
                    'created_at' => now()->subDays($index),
                ]);
        }

        $response = $this->get(route('medicines.index'))->assertOk();
        $medicines = $response->viewData('medicines');

        $this->assertCount(5, $medicines);
        $this->assertSame('Препарат 1', $medicines->first()->name);
        $this->assertSame(6, $medicines->total());

        $secondPage = $this->get(route('medicines.index', ['page' => 2]))
            ->assertOk()
            ->viewData('medicines');

        $this->assertCount(1, $secondPage);
    }

    public function test_medicine_list_searches_by_name_and_code(): void
    {
        $manufacturer = Manufacturer::factory()->create();
        $match = Medicine::factory()->for($manufacturer)->create([
            'code' => 'QA-0001',
            'name' => 'Парацетамол',
        ]);
        Medicine::factory()->for($manufacturer)->create([
            'code' => 'QA-0002',
            'name' => 'Ибупрофен',
        ]);

        $this->get(route('medicines.index', ['q' => 'Парацетамол']))
            ->assertOk()
            ->assertSeeText('Парацетамол')
            ->assertDontSeeText('Ибупрофен');

        $this->get(route('medicines.index', ['q' => 'QA-0001']))
            ->assertOk()
            ->assertSeeText($match->name)
            ->assertDontSeeText('Ибупрофен');
    }

    public function test_unrecognized_sort_value_uses_safe_default_ordering(): void
    {
        $manufacturer = Manufacturer::factory()->create();
        Medicine::factory()->for($manufacturer)->create(['code' => 'QA-0001']);

        $this->get(route('medicines.index', ['sort' => 'name desc, (SELECT 1)']))
            ->assertOk();

        $this->assertDatabaseCount('medicines', 1);
    }

    public function test_user_can_create_edit_and_delete_a_medicine(): void
    {
        $manufacturer = Manufacturer::factory()->create();
        $data = $this->validMedicineData($manufacturer);

        $this->post(route('medicines.store'), $data)
            ->assertRedirect(route('medicines.index'))
            ->assertSessionHas('status', 'Добавлено: Новый препарат (код QA-7001).');

        $medicine = Medicine::where('code', 'QA-7001')->firstOrFail();
        $this->assertModelExists($medicine);

        $this->get(route('medicines.edit', $medicine))
            ->assertOk()
            ->assertSee('value="QA-7001"', false);

        $data['name'] = 'Обновлённый препарат';

        $this->put(route('medicines.update', $medicine), $data)
            ->assertRedirect(route('medicines.index'))
            ->assertSessionHas('status', 'Изменения сохранены: Обновлённый препарат.');

        $this->assertDatabaseHas('medicines', [
            'id' => $medicine->id,
            'name' => 'Обновлённый препарат',
            'code' => 'QA-7001',
        ]);

        $this->delete(route('medicines.destroy', $medicine))
            ->assertRedirect(route('medicines.index'))
            ->assertSessionHas('status', 'Удалено: Обновлённый препарат.');

        $this->assertModelMissing($medicine);
    }

    public function test_short_medicine_name_returns_message_and_old_input(): void
    {
        $manufacturer = Manufacturer::factory()->create();
        $data = $this->validMedicineData($manufacturer);
        $data['name'] = 'Нос';

        $this->from(route('medicines.create'))
            ->post(route('medicines.store'), $data)
            ->assertRedirect(route('medicines.create'))
            ->assertSessionHasErrors([
                'name' => 'Название должно содержать не менее 4 символов.',
            ])
            ->assertSessionHasInput('name', 'Нос');

        $this->assertDatabaseCount('medicines', 0);
    }

    public function test_nonexistent_manufacturer_id_is_rejected(): void
    {
        $data = $this->validMedicineData(Manufacturer::factory()->create());
        $data['manufacturer_id'] = PHP_INT_MAX;

        $this->from(route('medicines.create'))
            ->post(route('medicines.store'), $data)
            ->assertSessionHasErrors([
                'manufacturer_id' => 'Выбранный производитель не существует.',
            ]);

        $this->assertDatabaseCount('medicines', 0);
    }

    public function test_code_must_match_the_variant_code_format(): void
    {
        $data = $this->validMedicineData(Manufacturer::factory()->create());
        $data['code'] = 'md-01';

        $this->from(route('medicines.create'))
            ->post(route('medicines.store'), $data)
            ->assertSessionHasErrors([
                'code' => 'Код должен иметь формат AA-0000 (две заглавные латинские буквы и четыре цифры).',
            ]);
    }

    public function test_duplicate_medicine_code_is_rejected(): void
    {
        $manufacturer = Manufacturer::factory()->create();
        Medicine::factory()->for($manufacturer)->create(['code' => 'QA-0001']);
        $data = $this->validMedicineData($manufacturer);
        $data['code'] = 'QA-0001';

        $this->from(route('medicines.create'))
            ->post(route('medicines.store'), $data)
            ->assertSessionHasErrors([
                'code' => 'Такой код препарата уже используется.',
            ]);

        $this->assertDatabaseCount('medicines', 1);
    }

    public function test_nonpositive_price_is_rejected(): void
    {
        $data = $this->validMedicineData(Manufacturer::factory()->create());
        $data['price'] = '-1';

        $this->from(route('medicines.create'))
            ->post(route('medicines.store'), $data)
            ->assertSessionHasErrors([
                'price' => 'Цена должна быть больше нуля.',
            ]);
    }

    public function test_html_in_medicine_name_is_escaped(): void
    {
        $manufacturer = Manufacturer::factory()->create();
        $medicine = Medicine::factory()->for($manufacturer)->create([
            'name' => '<script>alert(1)</script>',
        ]);

        $this->get(route('catalog.show', $medicine->id))
            ->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    /**
     * @return array<string, int|string|bool>
     */
    private function validMedicineData(Manufacturer $manufacturer): array
    {
        return [
            'manufacturer_id' => $manufacturer->id,
            'code' => 'QA-7001',
            'name' => 'Новый препарат',
            'form' => 'Таблетки',
            'dosage_mg' => 250,
            'price' => '1250.00',
            'by_prescription' => false,
        ];
    }
}
