<?php

namespace App\Models;

use App\Enums\CurrencyCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    /** @use HasFactory<\Database\Factories\ExchangeRateFactory> */
    use HasFactory;

    protected $fillable = [
        'from_currency',
        'to_currency',
        'rate',
        'source',
        'is_active',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'from_currency' => CurrencyCode::class,
            'to_currency' => CurrencyCode::class,
            'rate' => 'decimal:8',
            'is_active' => 'boolean',
        ];
    }
}
