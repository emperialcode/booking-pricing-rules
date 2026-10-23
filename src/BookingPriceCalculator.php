<?php

declare(strict_types=1);

namespace Portfolio\Pricing;

use InvalidArgumentException;

/**
 * Framework-independent domain service, suitable for injecting into Laravel.
 * All monetary inputs and outputs are in minor units (e.g. USD cents).
 */
final class BookingPriceCalculator
{
    /**
     * @param array<string, int> $unitPricesCents e.g. adult => 2500
     * @param array<string, int> $guests e.g. adult => 2
     * @return array{total_cents:int, breakdown:array<string, array{quantity:int,unit_cents:int,subtotal_cents:int}>}
     */
    public function calculate(array $unitPricesCents, array $guests, int $minimumGuests = 1, ?int $maximumGuests = null): array
    {
        if ($minimumGuests < 0 || ($maximumGuests !== null && $maximumGuests < $minimumGuests)) {
            throw new InvalidArgumentException('Invalid booking capacity bounds.');
        }

        if ($guests === []) {
            throw new InvalidArgumentException('At least one guest category is required.');
        }

        $guestCount = 0;
        $totalCents = 0;
        $breakdown = [];

        foreach ($guests as $category => $quantity) {
            if (!is_string($category) || $category === '' || !is_int($quantity) || $quantity < 0) {
                throw new InvalidArgumentException('Guest quantities must be non-negative integers with named categories.');
            }
            if (!array_key_exists($category, $unitPricesCents)) {
                throw new InvalidArgumentException("Missing price for category: {$category}");
            }
            $unit = $unitPricesCents[$category];
            if (!is_int($unit) || $unit < 0) {
                throw new InvalidArgumentException('Unit prices must be non-negative integer minor units.');
            }
            $guestCount += $quantity;
            $subtotal = $unit * $quantity;
            $totalCents += $subtotal;
            $breakdown[$category] = [
                'quantity' => $quantity,
                'unit_cents' => $unit,
                'subtotal_cents' => $subtotal,
            ];
        }

        if ($guestCount < $minimumGuests || ($maximumGuests !== null && $guestCount > $maximumGuests)) {
            throw new InvalidArgumentException('Booking guest count is outside permitted bounds.');
        }

        return ['total_cents' => $totalCents, 'breakdown' => $breakdown];
    }
}
