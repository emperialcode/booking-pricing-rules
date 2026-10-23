# Booking Pricing Rules — PHP / Laravel Domain Service Demo

A small, **standalone portfolio example** of reservation pricing logic. Designed as a dependency-free, framework-agnostic domain service that can be used inside a Laravel app.

This is an original demonstration project, **not** extracted from a client or private production repository.

## Features

- Per-guest-category integer-money pricing (adult, child, infant)
- Configurable minimum and maximum guest counts
- Explicit input validation and error handling
- Itemized pricing breakdown, with deterministic output
- Dependency-free smoke tests

## Run tests

Requires PHP 8.2 or later:

```bash
php tests/run.php
```

## Example

```php
require 'src/BookingPriceCalculator.php';

$calculator = new \Portfolio\Pricing\BookingPriceCalculator();
$quote = $calculator->calculate(
    ['adult' => 2500, 'child' => 1500, 'infant' => 0],
    ['adult' => 2, 'child' => 1, 'infant' => 1],
    minimumGuests: 1,
    maximumGuests: 6,
);

// $quote['total_cents'] === 6500, or $65.00
```

## Design notes

Money is represented in **integer minor units** to avoid floating-point rounding surprises. The service does not access a database or payment gateway; orchestration belongs to the application layer.

This demo intentionally does **not** implement taxes, discounts, inventory locking, currency conversion, or booking checkout. Those would require further business rules and tests.

## Potential Laravel integration

Inject `BookingPriceCalculator` into an application service or controller. Validate the incoming request and use this service to calculate a quote; persist a versioned snapshot before any payment/booking workflow.

## License

MIT — see LICENSE.
