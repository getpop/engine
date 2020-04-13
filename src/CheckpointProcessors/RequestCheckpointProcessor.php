<?php
namespace PoP\Engine\CheckpointProcessors;

use PoP\ComponentModel\CheckpointProcessorBase;
use PoP\ComponentModel\Error;

class RequestCheckpointProcessor extends CheckpointProcessorBase
{
    public const DOING_POST = 'doing-post';

    public function getCheckpointsToProcess()
    {
        return array(
            [self::class, self::DOING_POST],
        );
    }

    public function process(array $checkpoint)
    {
        switch ($checkpoint[1]) {
            case self::DOING_POST:
                if (!doingPost()) {
                    return new Error('notdoingpost');
                }
                break;
        }

        return parent::process($checkpoint);
    }
}
