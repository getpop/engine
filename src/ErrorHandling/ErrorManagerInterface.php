<?php

declare(strict_types=1);

namespace PoP\Engine\ErrorHandling;

use PoP\ComponentModel\Error;

interface ErrorManagerInterface
{
    public function convertFromCMSToPoPError(object $cmsError): Error;
    public function isCMSError(object $object): bool;
}
