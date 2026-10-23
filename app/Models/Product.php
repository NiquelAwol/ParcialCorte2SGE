<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'is_special_control',
        'invima_registration',
        'status',
    ];

    /**
     * Casts de los atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'is_special_control' => 'boolean',
            'status' => 'boolean',
        ];
    }

    /**
     * Relación: Un producto tiene muchos lotes.
     */
    public function lots(): HasMany
    {
        return $this->hasMany(Lot::class, 'product_id');
    }
}
