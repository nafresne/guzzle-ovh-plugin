<?php

namespace  Nafresne\GuzzleHttp\Event;

use GuzzleHttp\Event\Emitter;

use Nafresne\OvhSubscriber;

/**
 * OvhEmitter
 */
class OvhEmitter extends Emitter
{
    public function __construct(OvhSubscriber $ovhSubscriber)
    {
        $this->attach($ovhSubscriber);
    }
}