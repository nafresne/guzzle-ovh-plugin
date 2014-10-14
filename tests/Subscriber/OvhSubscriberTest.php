<?php

namespace Nafresne\Test\GuzzleHttp\Subscriber;

use Nafresne\GuzzleHttp\Subscriber\OvhSubscriber;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Subscriber\Mock;
use GuzzleHttp\Request;

/**
 * @covers Nafresne\GuzzleHttp\Subscriber\OvhSubscriber
 */
class OvhSubscriberTest extends \PHPUnit_Framework_TestCase
{
    protected $config;

    public function __construct()
    {
        $this->config = array(
            'application_key' => 'testApplicationKey',
            'application_secret' => 'testApplicationSecret',
            'consumer_key' => 'testConsumerKey'
        );
    }

    protected function createSubscriber()
    {
        return new OvhSubscriber($this->config);
    }

    protected function createClient()
    {
        $client = new Client();

        $mock = new Mock(array(new Response(200)));
        $client->getEmitter()->attach($mock);

        return $client;
    }


    public function testGetSignature()
    {
        $subscriber = $this->createSubscriber();
        $client = $this->createClient();
        $request = $client->createRequest('GET', 'http://example.com');

        $reflectionOfUser = new \ReflectionClass('Nafresne\GuzzleHttp\Subscriber\OvhSubscriber');
        $getSignatureMethod = $reflectionOfUser->getMethod('getSignature');
        $getSignatureMethod->setAccessible(true);

        $method = 'GET';
        $url = 'http://example.com';
        $body = '';
        $time = time();

        $schema = $this->config['application_secret'].'+'.$this->config['consumer_key'].'+';
        $schema .= $method.'+'.$url.'+'.$body.'+'. $time;

        $signature = '$1$' . sha1($schema);

        $this->assertEquals($signature, $getSignatureMethod->invokeArgs($subscriber, array($request, $time)));
    }

    public function testCreateOvhHeader()
    {
        $subscriber = $this->createSubscriber();
        $client = $this->createClient();

        $request = $client->createRequest('GET', 'http://example.com');
        $signature = '$1$' . sha1('signature');
        $time = time();

        $reflectionOfUser = new \ReflectionClass('Nafresne\GuzzleHttp\Subscriber\OvhSubscriber');
        $getSignatureMethod = $reflectionOfUser->getMethod('createOvhHeader');
        $getSignatureMethod->setAccessible(true);

        $getSignatureMethod->invokeArgs($subscriber, array($request, $signature, $time));

        $this->assertEquals($this->config['application_key'], $request->getHeader('X-Ovh-Application'));
        $this->assertEquals($this->config['consumer_key'], $request->getHeader('X-Ovh-Consumer'));
        $this->assertEquals($signature, $request->getHeader('X-Ovh-Signature'));
        $this->assertEquals($time, $request->getHeader('X-Ovh-Timestamp'));
        $this->assertEquals('application/json', $request->getHeader('Content-Type'));
    }


}