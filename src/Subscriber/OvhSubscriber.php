<?php

namespace  Nafresne\GuzzleHttp\Subscriber;

use GuzzleHttp\Message\RequestInterface;
use GuzzleHttp\Event\EmitterInterface;
use GuzzleHttp\Event\SubscriberInterface;
use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\RequestEvents;
Use GuzzleHttp\Client;
use GuzzleHttp\Collection;

/**
 * OVH signing plugin
 */
class OvhSubscriber implements SubscriberInterface
{
    /** @var Collection Configuration settings */
    protected $config;

    /**
     * Create a new OAuth 1.0 plugin
     *
     * @param array $config Configuration array containing these parameters:
     *     - string version
     */
    public function __construct($config)
    {
        $this->config = Collection::fromConfig($config, array(
            'application_key' => 'anonymous',
            'application_secret' => 'anonymous',
            'consumer_key' => 'anonymous'
        ), array(
            'application_key', 'application_secret', 'consumer_key'
        ));

        ladybug::dump($this->config);
    }
}