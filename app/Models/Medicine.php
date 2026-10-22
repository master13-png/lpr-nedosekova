<?php

namespace App\Models;

use Database\Factories\MedicineFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Medicine extends Model
{
    /** @use HasFactory<MedicineFactory> */
    use HasFactory;

    protected $fillable = [
        'manufacturer_id',
        'code',
        'name',
        'form',
        'dosage_mg',
        'price',
        'by_prescription',
    ];

    protected function casts(): array
    {
        return [
            'dosage_mg' => 'integer',
            'price' => 'decimal:2',
            'by_prescription' => 'boolean',
        ];
    }

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class);
    }
}
