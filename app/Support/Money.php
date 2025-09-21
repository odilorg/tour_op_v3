<?php

namespace App\Support;

use Brick\Money\Money as BrickMoney;

class Money
{
    public static function fromMinor(int $amountMinor, string $currency): BrickMoney
    {
        return BrickMoney::ofMinor($amountMinor, $currency);
    }

    public static function format(int $amountMinor, string $currency): string
    {
        if (class_exists(\NumberFormatter::class)) {
            return self::fromMinor($amountMinor, $currency)->formatTo('en_US');
        }

        $amount = $amountMinor / 100;

        return sprintf('%s %.2f', $currency, $amount);
    }
}
