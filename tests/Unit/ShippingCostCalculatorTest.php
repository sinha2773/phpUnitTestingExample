<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\ShippingCostCalculator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ShippingCostCalculatorTest extends TestCase
{
    public function test_it_calculates_local_shipping_cost(): void
    {
        $calculator = new ShippingCostCalculator();

        $shippingCost = $calculator->calculate(49.99, 2.50, 'local');

        $this->assertSame(5.00, $shippingCost);
    }

    public function test_it_calculates_national_shipping_cost(): void
    {
        $calculator = new ShippingCostCalculator();

        $shippingCost = $calculator->calculate(75.00, 4.00, 'national');

        $this->assertSame(12.50, $shippingCost);
    }

    public function test_it_applies_heavy_package_surcharge(): void
    {
        $calculator = new ShippingCostCalculator();

        $shippingCost = $calculator->calculate(75.00, 6.25, 'national');

        $this->assertSame(22.50, $shippingCost);
    }

    public function test_it_applies_free_shipping_for_eligible_local_orders(): void
    {
        $calculator = new ShippingCostCalculator();

        $shippingCost = $calculator->calculate(100.00, 3.00, 'local');

        $this->assertSame(0.00, $shippingCost);
    }

    public function test_free_shipping_does_not_apply_to_international_orders(): void
    {
        $calculator = new ShippingCostCalculator();

        $shippingCost = $calculator->calculate(150.00, 3.00, 'international');

        $this->assertSame(35.00, $shippingCost);
    }

    public function test_it_rejects_negative_order_total(): void
    {
        $calculator = new ShippingCostCalculator();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Order total cannot be negative.');

        $calculator->calculate(-1.00, 1.00, 'local');
    }

    public function test_it_rejects_zero_package_weight(): void
    {
        $calculator = new ShippingCostCalculator();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Package weight must be greater than zero.');

        $calculator->calculate(50.00, 0.00, 'local');
    }

    public function test_it_rejects_unsupported_shipping_zone(): void
    {
        $calculator = new ShippingCostCalculator();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported shipping zone.');

        $calculator->calculate(50.00, 1.00, 'express');
    }
}
