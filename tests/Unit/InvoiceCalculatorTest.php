<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\InvoiceCalculator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class InvoiceCalculatorTest extends TestCase
{
    public function test_it_calculates_subtotal(): void
    {
        $calculator = new InvoiceCalculator();

        $subtotal = $calculator->subtotal([
            ['name' => 'Keyboard', 'price' => 50.00, 'quantity' => 2],
            ['name' => 'Mouse', 'price' => 25.50, 'quantity' => 1],
        ]);

        $this->assertSame(125.50, $subtotal);
    }

    public function test_it_calculates_total_with_tax(): void
    {
        $calculator = new InvoiceCalculator();

        $total = $calculator->totalWithTax([
            ['name' => 'Monitor', 'price' => 200.00, 'quantity' => 1],
        ], 0.10);

        $this->assertSame(220.00, $total);
    }

    public function test_it_applies_discount(): void
    {
        $calculator = new InvoiceCalculator();

        $total = $calculator->totalAfterDiscount([
            ['name' => 'Desk', 'price' => 150.00, 'quantity' => 1],
        ], 40.00);

        $this->assertSame(110.00, $total);
    }

    public function test_discount_never_makes_total_negative(): void
    {
        $calculator = new InvoiceCalculator();

        $total = $calculator->totalAfterDiscount([
            ['name' => 'USB Cable', 'price' => 10.00, 'quantity' => 1],
        ], 50.00);

        $this->assertSame(0.00, $total);
    }

    public function test_it_rejects_negative_item_price(): void
    {
        $calculator = new InvoiceCalculator();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Item price must be zero or greater.');

        $calculator->subtotal([
            ['name' => 'Invalid Item', 'price' => -5.00, 'quantity' => 1],
        ]);
    }

    public function test_it_rejects_zero_quantity(): void
    {
        $calculator = new InvoiceCalculator();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Item quantity must be a positive integer.');

        $calculator->subtotal([
            ['name' => 'Invalid Item', 'price' => 5.00, 'quantity' => 0],
        ]);
    }
}
