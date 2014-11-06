<?php

namespace  Nafresne\GuzzleHttp\Subscriber;

use GuzzleHttp\Event\SubscriberInterface;
use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\RequestEvents;
use GuzzleHttp\Collection;
use GuzzleHttp\Message\Request;

/**
 * OVH signing plugin
 */
class OvhSubscriber implements SubscriberInterface
{
    /** @var Collection Configuration settings */
    protected $config;

    /** @var string OVH application key */
    protected $applicationKey;

    /** @var string OVH application secret */
    protected $applicationSecret;

    /** @var string OVH consumer key */
    protected $consumerKey;

    /**
     * Create a OVH subscriber
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

        $this->applicationKey    = $this->config['application_key'];
        $this->applicationSecret = $this->config['application_secret'];
        $this->consumerKey       = $this->config['consumer_key'];
    }

    /**
     * {@inheritdoc}
     */
    public function getEvents()
    {
        return array(
            'before' => array('onBefore', RequestEvents::SIGN_REQUEST)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function onBefore(BeforeEvent $event, $name)
    {
        $client = $event->getClient();
        $request = $event->getRequest();

        $time = time() + $client->getTimeDrift();
        $authenticated = $client->isAuthenticated();
        $signature = null;

        if ($authenticated) {
            $signature = $this->getSignature($request, $time);
        }

        $this->createOvhHeader($request, $signature, $time);
    }

    /**
     * Create the OVH header
     * @param Request $request
     * @param string  $signature
     * @param int     $time
     */
    protected function createOvhHeader(Request $request, $signature, $time)
    {
        $request->setHeader('Content-Type', 'application/json');
        $request->setHeader('X-Ovh-Application', $this->applicationKey);

        if ($signature) {
            $request->setHeader('X-Ovh-Consumer', $this->consumerKey);
            $request->setHeader('X-Ovh-Signature', $signature);
            $request->setHeader('X-Ovh-Timestamp', $time);
        }
    }

    /**
     * Create the OVH signature
     * @param Request $request
     * @param int     $time
     *
     * @return string
     */
    protected function getSignature(Request $request, $time)
    {
        $method = $request->getMethod();
        $url    = $request->getUrl();
        $body   = $request->getBody();

        $schema = $this->applicationSecret.'+'.$this->consumerKey.'+'.$method.'+'.$url.'+'.$body.'+'. $time;

        return '$1$' . sha1($schema);
    }

    /**
     * Set timeDrift
     * @param int $timeDrift
     */
    public function setTimeDrift($timeDrift)
    {
        $this->timeDrift = (int) $timeDrift;
    }

    public function setAuthenticated($authenticated)
    {
        $this->authenticated = $authenticated;
    }
}