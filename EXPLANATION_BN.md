# PHPUnit শেখার জন্য এই PHP Codebase ব্যাখ্যা

এই প্রজেক্টটি একটি ছোট PHP Unit Testing example। এখানে framework ব্যবহার করা হয়নি, যাতে আপনি সরাসরি বুঝতে পারেন PHPUnit কীভাবে কাজ করে।

## Project Structure

```text
.
├── composer.json
├── composer.lock
├── phpunit.xml
├── src
│   └── InvoiceCalculator.php
├── tests
│   └── Unit
│       └── InvoiceCalculatorTest.php
├── README.md
├── EXPLANATION_BN.md
└── .gitignore
```

## এই প্রজেক্টের মূল ধারণা

এই প্রজেক্টে আমরা একটি invoice calculation class test করছি।

`InvoiceCalculator` class-এর কাজ:

- invoice subtotal calculate করা
- tax যোগ করে total calculate করা
- discount apply করা
- invalid data হলে exception throw করা

`InvoiceCalculatorTest` class-এর কাজ:

- production code ঠিকমতো কাজ করছে কিনা verify করা
- valid input দিলে expected result আসছে কিনা দেখা
- invalid input দিলে expected exception আসছে কিনা দেখা

## composer.json

এই ফাইলটি PHP project dependency এবং autoloading manage করে।

```json
"require": {
    "php": "^8.2"
}
```

এর মানে এই project PHP 8.2 বা compatible newer version চায়।

```json
"require-dev": {
    "phpunit/phpunit": "^11.0"
}
```

এর মানে PHPUnit শুধু development/testing environment-এর জন্য install হবে। Production code চালানোর জন্য PHPUnit দরকার নেই।

```json
"autoload": {
    "psr-4": {
        "App\\": "src/"
    }
}
```

এর মানে `App\InvoiceCalculator` class খুঁজতে Composer `src/InvoiceCalculator.php` ফাইল ব্যবহার করবে।

```json
"autoload-dev": {
    "psr-4": {
        "Tests\\": "tests/"
    }
}
```

এর মানে test class গুলো `Tests\` namespace দিয়ে `tests/` folder থেকে autoload হবে।

```json
"scripts": {
    "test": "phpunit"
}
```

এর ফলে আপনি এই command দিয়ে test চালাতে পারবেন:

```bash
composer test
```

## phpunit.xml

এই ফাইল PHPUnit-এর configuration।

```xml
bootstrap="vendor/autoload.php"
```

PHPUnit test চালানোর আগে Composer autoloader load করবে। এর ফলে class manually require করতে হয় না।

```xml
<directory>tests/Unit</directory>
```

PHPUnit জানে test file কোথায় খুঁজতে হবে।

```xml
<directory>src</directory>
```

এটি source code directory নির্দেশ করে। Coverage বা source analysis দরকার হলে PHPUnit এই folder বিবেচনা করবে।

## src/InvoiceCalculator.php

এটাই production code।

```php
namespace App;
```

এই class `App` namespace-এর মধ্যে আছে। তাই test file থেকে import করা হয়েছে:

```php
use App\InvoiceCalculator;
```

### subtotal() method

```php
public function subtotal(array $items): float
```

এই method invoice item list নিয়ে subtotal return করে।

Example input:

```php
[
    ['name' => 'Keyboard', 'price' => 50.00, 'quantity' => 2],
    ['name' => 'Mouse', 'price' => 25.50, 'quantity' => 1],
]
```

Calculation:

```text
Keyboard = 50.00 * 2 = 100.00
Mouse    = 25.50 * 1 = 25.50
Subtotal = 125.50
```

Code:

```php
$subtotal += $item['price'] * $item['quantity'];
```

শেষে result 2 decimal place-এ round করা হয়:

```php
return round($subtotal, 2);
```

### totalWithTax() method

```php
public function totalWithTax(array $items, float $taxRate): float
```

এই method subtotal-এর সাথে tax যোগ করে final total return করে।

Example:

```text
Subtotal = 200.00
Tax rate = 0.10
Tax      = 200.00 * 0.10 = 20.00
Total    = 220.00
```

যদি tax rate negative হয়, তাহলে exception throw করা হয়:

```php
if ($taxRate < 0) {
    throw new InvalidArgumentException('Tax rate cannot be negative.');
}
```

### totalAfterDiscount() method

```php
public function totalAfterDiscount(array $items, float $discountAmount): float
```

এই method subtotal থেকে discount বাদ দেয়।

Example:

```text
Subtotal = 150.00
Discount = 40.00
Total    = 110.00
```

Important logic:

```php
return round(max(0, $subtotal - $discountAmount), 2);
```

এখানে `max(0, ...)` ব্যবহার করা হয়েছে যাতে discount বেশি হলেও total কখনো negative না হয়।

Example:

```text
Subtotal = 10.00
Discount = 50.00
Total    = 0.00
```

### validateItem() method

```php
private function validateItem(array $item): void
```

এই private method প্রতিটি invoice item valid কিনা check করে।

প্রতিটি item-এ থাকতে হবে:

- name
- price
- quantity

এই validation fail করলে exception throw হবে:

```php
throw new InvalidArgumentException('Invoice item requires name, price, and quantity.');
```

Price negative হলে error:

```php
throw new InvalidArgumentException('Item price must be zero or greater.');
```

Quantity positive integer না হলে error:

```php
throw new InvalidArgumentException('Item quantity must be a positive integer.');
```

## tests/Unit/InvoiceCalculatorTest.php

এটি unit test file।

```php
use PHPUnit\Framework\TestCase;
```

প্রতিটি PHPUnit test class সাধারণত `TestCase` extend করে।

```php
final class InvoiceCalculatorTest extends TestCase
```

এই class-এর প্রতিটি public method যার নাম `test` দিয়ে শুরু, সেটি PHPUnit test হিসেবে চালাবে।

## Unit Test Pattern: Arrange, Act, Assert

Unit test সাধারণত ৩ ধাপে লেখা হয়।

### 1. Arrange

যে object এবং input data দরকার, তা প্রস্তুত করা।

```php
$calculator = new InvoiceCalculator();
```

### 2. Act

যে method test করতে চান, সেটি call করা।

```php
$subtotal = $calculator->subtotal($items);
```

### 3. Assert

Expected result-এর সাথে actual result match করছে কিনা check করা।

```php
$this->assertSame(125.50, $subtotal);
```

## Test: subtotal calculation

```php
public function test_it_calculates_subtotal(): void
```

এই test check করে subtotal ঠিকমতো calculate হচ্ছে কিনা।

Input:

```php
[
    ['name' => 'Keyboard', 'price' => 50.00, 'quantity' => 2],
    ['name' => 'Mouse', 'price' => 25.50, 'quantity' => 1],
]
```

Expected:

```php
125.50
```

Assertion:

```php
$this->assertSame(125.50, $subtotal);
```

## Test: total with tax

```php
public function test_it_calculates_total_with_tax(): void
```

এই test check করে subtotal-এর সাথে tax correctly add হচ্ছে কিনা।

Input:

```php
price = 200.00
quantity = 1
taxRate = 0.10
```

Expected:

```php
220.00
```

## Test: discount

```php
public function test_it_applies_discount(): void
```

এই test check করে subtotal থেকে discount correctly subtract হচ্ছে কিনা।

Input:

```php
subtotal = 150.00
discount = 40.00
```

Expected:

```php
110.00
```

## Test: discount total negative না হওয়া

```php
public function test_discount_never_makes_total_negative(): void
```

এই test একটি business rule verify করে:

> Discount বেশি হলেও total কখনো negative হবে না।

Input:

```php
subtotal = 10.00
discount = 50.00
```

Expected:

```php
0.00
```

## Test: negative price reject করা

```php
public function test_it_rejects_negative_item_price(): void
```

এই test invalid input check করে।

```php
$this->expectException(InvalidArgumentException::class);
```

এর মানে PHPUnit আশা করছে এই test চলার সময় `InvalidArgumentException` throw হবে।

```php
$this->expectExceptionMessage('Item price must be zero or greater.');
```

এর মানে exception message-ও expected message-এর সাথে match করতে হবে।

## Test: zero quantity reject করা

```php
public function test_it_rejects_zero_quantity(): void
```

Quantity অবশ্যই positive integer হতে হবে। তাই `0` দিলে exception throw হবে।

## assertSame() কেন ব্যবহার করা হয়েছে?

```php
$this->assertSame(125.50, $subtotal);
```

`assertSame()` value এবং type দুইটাই check করে।

Example:

```php
125.50 === 125.50 // true
"125.50" === 125.50 // false
```

Unit test-এ strict assertion ভালো, কারণ এতে bug দ্রুত ধরা পড়ে।

## Exception Test কেন দরকার?

Production-ready code শুধু happy path test করলেই হয় না।

আপনাকে test করতে হবে:

- valid input
- invalid input
- edge case
- business rule
- exception behavior

এই project-এ invalid price এবং invalid quantity test করা হয়েছে।

## Test চালানোর command

Dependency install:

```bash
composer install
```

Test run:

```bash
composer test
```

Direct PHPUnit run:

```bash
./vendor/bin/phpunit
```

## Expected Output

```text
OK (6 tests, 8 assertions)
```

এর মানে:

- ৬টি test method run হয়েছে
- ৮টি assertion check হয়েছে
- সবগুলো pass করেছে

## এই Example থেকে কী শেখা উচিত

১. Business logic আলাদা class-এ রাখা ভালো।

২. Test code production code থেকে আলাদা folder-এ রাখা ভালো।

৩. প্রতিটি method-এর expected behavior test করা উচিত।

৪. শুধু successful case না, invalid input-ও test করা উচিত।

৫. Unit test ছোট, focused, fast, repeatable হওয়া উচিত।

৬. Test method name এমন হওয়া উচিত যাতে পড়ে বোঝা যায় কী behavior test হচ্ছে।

## Practical Learning Task

আরও practice করার জন্য আপনি নিজে এই features যোগ করতে পারেন:

১. `totalWithTax()` method negative tax rate reject করছে কিনা test করুন।

২. Empty invoice item list দিলে subtotal `0.00` হয় কিনা test করুন।

৩. Missing `name`, `price`, বা `quantity` থাকলে exception হয় কিনা test করুন।

৪. Percentage discount feature add করে test লিখুন।

## Senior Developer Advice

Unit test লেখার সময় মনে রাখবেন:

- implementation detail test করবেন না
- public behavior test করবেন
- private method সরাসরি test করবেন না
- private method-এর behavior public method-এর মাধ্যমে test করবেন
- test readable হওয়া production code-এর মতোই গুরুত্বপূর্ণ

এই codebase-এ `validateItem()` private method। আমরা এটিকে সরাসরি test করিনি। বরং `subtotal()` method call করে validation behavior test করেছি। এটিই ভালো practice।
