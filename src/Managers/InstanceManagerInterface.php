<?php
namespace PoP\Engine\Managers;

interface InstanceManagerInterface
{
    public function getInstance(string $class);
}
