<?php

namespace Nafresne\GuzzleHttp;

use GuzzleHttp\Client;
use Nafresne\GuzzleHttp\Subscriber\OvhSubscriber;

/**
 * OVH Client
 */
class OvhClient extends Client
{
    /** @var string Ovh auth time Url used by the client */
    protected $timeDriftUrl;

    /**
     * {@inheritdoc}
     * @throw \InvalidArgumentException if time_url missing
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->configureTimeUrl($config);
        $this->configureTimeDrift();
    }

    /**
     * Configure Time Url
     * @param array $config Client configuration settings
     */
    protected function configureTimeUrl($config)
    {
        if (!array_key_exists('time_url', $config)) {
            throw new \InvalidArgumentException("Missing parameter 'time_url'");
        }

        $this->timeDriftUrl = $config['time_url'];
    }

    /**
     * Call OVH Api to set time drift
     */
    protected function configureTimeDrift()
    {
        $response = $this->get($this->getBaseUrl() . $this->timeDriftUrl);

        if ($this->getEmitter() instanceof OvhSubscriber) {
            $this->getEmitter()->setTimeDrift(time() - (int) $response->getBody()->__toString());
        }
    }
}