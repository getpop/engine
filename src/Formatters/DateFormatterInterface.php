<?php

declare(strict_types=1);

namespace PoP\Engine\Formatters;

interface DateFormatterInterface
{
    /**
     * Formatted date string or sum of Unix timestamp and timezone offset. False on failure.
     */
    public function format(string $format, string $date): string | int | false;
}
