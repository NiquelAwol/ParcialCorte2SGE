<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lot extends Model
{
    use HasFactory;

    protected $table = 'lots';

    protected $fillable = [
        'product_id',
        'lot_number',
        'expiration_date',
        'quantity',
    ];

    /**
     * Casts de los atributos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expiration_date' => 'date',
            'quantity' => 'integer',
        ];
    }

    /**
     * Relación: Un lote pertenece a un producto.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
