<?php
namespace PoP\Engine\Engine;

interface EngineInterface extends \PoP\ComponentModel\Engine\EngineInterface
{
    public function outputResponse(): void;
}
