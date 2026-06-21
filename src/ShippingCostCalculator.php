<?php

declare(strict_types=1);

namespace App;

use InvalidArgumentException;

final class ShippingCostCalculator
{
    private const FREE_SHIPPING_THRESHOLD = 100.00;
    private const HEAVY_PACKAGE_THRESHOLD_KG = 5.00;
    private const HEAVY_PACKAGE_SURCHARGE = 10.00;

    /**
     * @var array<string, float>
     */
    private const BASE_RATES = [
        'local' => 5.00,
        'national' => 12.50,
        'international' => 35.00,
    ];

    public function calculate(float $orderTotal, float $packageWeightKg, string $shippingZone): float
    {
        $this->validateOrderTotal($orderTotal);
        $this->validatePackageWeight($packageWeightKg);
        $this->validateShippingZone($shippingZone);

        if ($orderTotal >= self::FREE_SHIPPING_THRESHOLD && $shippingZone !== 'international') {
            return 0.00;
        }

        $shippingCost = self::BASE_RATES[$shippingZone];

        if ($packageWeightKg > self::HEAVY_PACKAGE_THRESHOLD_KG) {
            $shippingCost += self::HEAVY_PACKAGE_SURCHARGE;
        }

        return round($shippingCost, 2);
    }

    private function validateOrderTotal(float $orderTotal): void
    {
        if ($orderTotal < 0) {
            throw new InvalidArgumentException('Order total cannot be negative.');
        }
    }

    private function validatePackageWeight(float $packageWeightKg): void
    {
        if ($packageWeightKg <= 0) {
            throw new InvalidArgumentException('Package weight must be greater than zero.');
        }
    }

    private function validateShippingZone(string $shippingZone): void
    {
        if (! array_key_exists($shippingZone, self::BASE_RATES)) {
            throw new InvalidArgumentException('Unsupported shipping zone.');
        }
    }
}
