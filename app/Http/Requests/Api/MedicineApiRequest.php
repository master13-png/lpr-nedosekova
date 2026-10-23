<?php

namespace App\Http\Requests\Api;

use App\Models\Medicine;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MedicineApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $required = $this->isMethod('PATCH') ? 'sometimes' : 'required';
        $medicine = $this->route('medicine');
        $uniqueCode = Rule::unique('medicines', 'code');

        if ($medicine instanceof Medicine) {
            $uniqueCode->ignore($medicine->getKey());
        }

        return [
            'manufacturer_id' => [$required, 'integer', Rule::exists('manufacturers', 'id')],
            'code' => [
                $required,
                'string',
                'regex:/^[A-Z]{2}-[0-9]{4}$/',
                $uniqueCode,
            ],
            'name' => [
                $required,
                'string',
                'min:'.config('lab.min_len'),
                'max:'.config('lab.str_len'),
            ],
            'form' => [$required, 'string', 'max:50'],
            'dosage_mg' => [$required, 'integer', 'min:1', 'max:10000'],
            'price' => [$required, 'numeric', 'decimal:0,2', 'min:0.01', 'max:99999999.99'],
            'by_prescription' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Поле «:attribute» обязательно.',
            'integer' => 'Поле «:attribute» должно быть целым числом.',
            'numeric' => 'Поле «:attribute» должно быть числом.',
            'exists' => 'Выбранный производитель не существует.',
            'regex' => 'Код должен иметь формат AA-0000 (две заглавные латинские буквы и четыре цифры).',
            'unique' => 'Такой код препарата уже используется.',
            'name.min' => 'Название должно содержать не менее :min символов.',
            'name.max' => 'Название должно содержать не более :max символов.',
            'form.max' => 'Форма выпуска должна содержать не более :max символов.',
            'dosage_mg.min' => 'Дозировка должна быть больше нуля.',
            'dosage_mg.max' => 'Дозировка не может превышать :max мг.',
            'price.decimal' => 'Цена должна содержать не более двух знаков после запятой.',
            'price.min' => 'Цена должна быть больше нуля.',
            'price.max' => 'Цена превышает допустимое значение.',
        ];
    }

    public function attributes(): array
    {
        return [
            'manufacturer_id' => 'Производитель',
            'code' => 'Код препарата',
            'name' => 'Название',
            'form' => 'Форма выпуска',
            'dosage_mg' => 'Дозировка',
            'price' => 'Цена',
            'by_prescription' => 'Отпуск по рецепту',
        ];
    }

    protected function prepareForValidation(): void
    {
        $aliases = [
            'manufacturer_id' => 'manufacturerId',
            'dosage_mg' => 'dosageMg',
            'by_prescription' => 'byPrescription',
        ];
        $normalized = [];

        foreach ($aliases as $field => $alias) {
            if ($this->exists($alias)) {
                $normalized[$field] = $this->input($alias);
            }
        }

        if ($normalized !== []) {
            $this->merge($normalized);
        }
    }
}
