<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DispensingService
{
    /**
     * Despacha / dispensa una cantidad determinada de un producto aplicando la regla FEFO.
     *
     * Reglas aplicadas:
     * 1. Ejecuta todo dentro de una transacción de base de datos (DB::transaction).
     * 2. Ignora los lotes vencidos (expiration_date < fecha actual).
     * 3. Valida que el stock vigente no vencido alcance para cubrir la cantidad solicitada.
     *    Si no alcanza, lanza ValidationException y no altera ningún dato.
     * 4. Descuenta primero del lote que vence antes (First Expired, First Out - FEFO).
     * 5. Descuenta la cantidad total despachada del stock general del producto.
     *
     * @param  Product  $product
     * @param  int  $quantity
     * @return Product
     *
     * @throws ValidationException
     */
    public function dispense(Product $product, int $quantity): Product
    {
        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'La cantidad a dispensar debe ser mayor a cero.',
            ]);
        }

        return DB::transaction(function () use ($product, $quantity) {
            $today = Carbon::today()->toDateString();

            // Bloquear el producto para actualizar concurrentemente
            $lockedProduct = Product::where('id', $product->id)->lockForUpdate()->firstOrFail();

            // Obtener los lotes no vencidos con cantidad mayor a cero ordenados por fecha de vencimiento ascendente (FEFO)
            $availableLots = $lockedProduct->lots()
                ->where('expiration_date', '>=', $today)
                ->where('quantity', '>', 0)
                ->orderBy('expiration_date', 'asc')
                ->lockForUpdate()
                ->get();

            // Validar que el stock total en lotes vigentes alcance la cantidad solicitada
            $totalAvailableStock = $availableLots->sum('quantity');

            if ($totalAvailableStock < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => "Stock insuficiente en lotes no vencidos. Solicitado: {$quantity}, disponible vigente: {$totalAvailableStock}.",
                ]);
            }

            // Validar también coherencia con el stock general del producto
            if ($lockedProduct->stock < $quantity) {
                throw ValidationException::withMessages([
                    'stock' => "El stock total del producto ({$lockedProduct->stock}) es inferior a la cantidad solicitada ({$quantity}).",
                ]);
            }

            // Descontar usando la estrategia FEFO (First Expired, First Out)
            $remainingToDeduct = $quantity;

            foreach ($availableLots as $lot) {
                if ($remainingToDeduct <= 0) {
                    break;
                }

                if ($lot->quantity <= $remainingToDeduct) {
                    $remainingToDeduct -= $lot->quantity;
                    $lot->quantity = 0;
                } else {
                    $lot->quantity -= $remainingToDeduct;
                    $remainingToDeduct = 0;
                }

                $lot->save();
            }

            // Descontar del stock total del producto
            $lockedProduct->stock -= $quantity;
            $lockedProduct->save();

            // Sincronizar el modelo recibido
            $product->refresh();

            return $product;
        });
    }
}
