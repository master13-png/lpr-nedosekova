<?php

namespace Tests\Feature;

use App\Models\Manufacturer;
use App\Models\Medicine;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class CatalogControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_existing_catalog_routes_render_database_medicines(): void
    {
        $manufacturer = Manufacturer::factory()->create([
            'name' => 'Проверенный производитель',
        ]);
        $medicine = Medicine::factory()->for($manufacturer)->create([
            'code' => 'QA-1234',
            'name' => 'Парацетамол',
        ]);

        $this->get('/')
            ->assertRedirect(route('catalog.index'));

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSeeText('Парацетамол')
            ->assertSeeText('Проверенный производитель')
            ->assertSeeText('F1-FUTI, J8-LAIP');

        $this->get(route('catalog.show', $medicine->id))
            ->assertOk()
            ->assertSeeText('QA-1234');

        $this->get(route('catalog.code', 'QA-1234'))
            ->assertOk()
            ->assertSeeText('Парацетамол');

        $this->get(route('catalog.manufacturer', $manufacturer->name))
            ->assertOk()
            ->assertSeeText('Парацетамол');
    }

    public function test_feedback_form_still_accepts_messages(): void
    {
        $this->post(route('feedback.send'), [
            'name' => 'Александра',
            'message' => 'Проверка формы обратной связи',
        ])
            ->assertRedirect(route('feedback.form'))
            ->assertSessionHas(
                'status',
                'Спасибо, Александра! Сообщение получено: Проверка формы обратной связи'
            );
    }
}
