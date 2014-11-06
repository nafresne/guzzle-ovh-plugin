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

    protected $timeDrift = 0;

    protected $authenticated = false;

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

        $this->authenticated = true;
        $this->timeDrift = (int) $response->getBody()->__toString() - \time();
    }

    public function isAuthenticated()
    {
        return $this->authenticated;
    }

    public function getTimeDrift()
    {
        return $this->timeDrift;
    }
}