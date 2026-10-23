<?php

declare(strict_types=1);

require __DIR__ . '/../src/BookingPriceCalculator.php';

use Portfolio\Pricing\BookingPriceCalculator;

$service = new BookingPriceCalculator();
$result = $service->calculate(
    ['adult' => 2500, 'child' => 1500, 'infant' => 0],
    ['adult' => 2, 'child' => 1, 'infant' => 1],
    minimumGuests: 1,
    maximumGuests: 6,
);
assertSame(6500, $result['total_cents']);
assertSame(5000, $result['breakdown']['adult']['subtotal_cents']);
assertSame(0, $result['breakdown']['infant']['subtotal_cents']);

$rejected = false;
try {
    $service->calculate(['adult' => 2500], ['adult' => 7], maximumGuests: 6);
} catch (InvalidArgumentException) {
    $rejected = true;
}
assertSame(true, $rejected);

function assertSame(mixed $expected, mixed $actual): void
{
    if ($expected !== $actual) {
        throw new RuntimeException('Assertion failed: '.var_export($expected, true).' !== '.var_export($actual, true));
    }
}

echo "4 assertions passed\n";
