<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;

final class InvoiceCalculator
{
    /**
     * @param array<int, array{name: string, price: float|int, quantity: int}> $items
     */
    public function subtotal(array $items): float
    {
        $subtotal = 0.0;

        foreach ($items as $item) {
            $this->validateItem($item);

            $subtotal += $item['price'] * $item['quantity'];
        }

        return round($subtotal, 2);
    }

    /**
     * @param array<int, array{name: string, price: float|int, quantity: int}> $items
     */
    public function totalWithTax(array $items, float $taxRate): float
    {
        if ($taxRate < 0) {
            throw new InvalidArgumentException('Tax rate cannot be negative.');
        }

        $subtotal = $this->subtotal($items);

        return round($subtotal + ($subtotal * $taxRate), 2);
    }

    /**
     * @param array<int, array{name: string, price: float|int, quantity: int}> $items
     */
    public function totalAfterDiscount(array $items, float $discountAmount): float
    {
        if ($discountAmount < 0) {
            throw new InvalidArgumentException('Discount amount cannot be negative.');
        }

        $subtotal = $this->subtotal($items);

        return round(max(0, $subtotal - $discountAmount), 2);
    }

    /**
     * @param array{name?: mixed, price?: mixed, quantity?: mixed} $item
     */
    private function validateItem(array $item): void
    {
        if (! isset($item['name'], $item['price'], $item['quantity'])) {
            throw new InvalidArgumentException('Invoice item requires name, price, and quantity.');
        }

        if (! is_numeric($item['price']) || $item['price'] < 0) {
            throw new InvalidArgumentException('Item price must be zero or greater.');
        }

        if (! is_int($item['quantity']) || $item['quantity'] <= 0) {
            throw new InvalidArgumentException('Item quantity must be a positive integer.');
        }
    }
}
