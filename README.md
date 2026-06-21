# PHPUnit Learning Example

This is a small PHP project created to explain unit testing with PHPUnit.

## What This Project Tests

The `InvoiceCalculator` class contains business logic:

- Calculate invoice subtotal
- Add tax
- Apply discount
- Validate invalid input

The `ShippingCostCalculator` class contains another real-life example:

- Calculate shipping by delivery zone
- Apply free shipping for eligible orders
- Add a heavy package surcharge
- Reject invalid order totals, package weights, and zones

The tests verify that this logic behaves correctly.

## File Structure

```text
.
├── composer.json
├── phpunit.xml
├── src
│   ├── InvoiceCalculator.php
│   └── ShippingCostCalculator.php
└── tests
    └── Unit
        ├── InvoiceCalculatorTest.php
        └── ShippingCostCalculatorTest.php
```

## Install Dependencies

```bash
composer install
```

## Run Tests

```bash
composer test
```

Or directly:

```bash
./vendor/bin/phpunit
```

## Unit Testing Basics

A unit test checks one small unit of code, usually one class or method.

Good unit tests are:

- Fast
- Isolated
- Repeatable
- Focused on one behavior
- Clear about expected output

## Example Test Flow

```php
$calculator = new InvoiceCalculator();

$subtotal = $calculator->subtotal([
    ['name' => 'Keyboard', 'price' => 50.00, 'quantity' => 2],
]);

$this->assertSame(100.00, $subtotal);
```

This follows the Arrange, Act, Assert pattern:

- Arrange: create the object and input data
- Act: call the method being tested
- Assert: verify the expected result

## Testing Exceptions

For invalid input, the code should fail clearly:

```php
$this->expectException(InvalidArgumentException::class);

$calculator->subtotal([
    ['name' => 'Invalid Item', 'price' => -5.00, 'quantity' => 1],
]);
```

This confirms the method protects itself from invalid data.
