<?php

namespace App\DataFixtures;

use Faker\Provider\Base;
use Faker\Provider\DateTime;

class FakerExtensions extends Base
{
    public static function immutableDateTimeBetween($startDate = '-6 months', $endDate = 'now', $timezone = null): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromMutable(
            DateTime::dateTimeBetween($startDate, $endDate, $timezone)
        );
    }
}
