#Guzzle OVH Plugin

[![PHP version](https://badge.fury.io/ph/nafresne%2Fguzzle-ovh-plugin.svg)](http://badge.fury.io/ph/nafresne%2Fguzzle-ovh-plugin)
[![Build Status](https://scrutinizer-ci.com/g/nafresne/guzzle-ovh-plugin/badges/build.png?b=master)](https://scrutinizer-ci.com/g/nafresne/guzzle-ovh-plugin/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nafresne/guzzle-ovh-plugin/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nafresne/guzzle-ovh-plugin/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/nafresne/guzzle-ovh-plugin/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/nafresne/guzzle-ovh-plugin/?branch=master)

Guzzle 4 Plugin to use OVH API

More informations on OVH API: https://api.ovh.com/

##Installation

###Install via composer

```shell
# Install Composer
curl -sS https://getcomposer.org/installer | php

# Add the plugin as a dependency
php composer.phar require nafresne/guzzle-ovh-plugin:dev-master
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```

##Basic usage

```php
require 'vendor/autoload.php';

use Nafresne\GuzzleHttp\OvhClient;
use Nafresne\GuzzleHttp\Subscriber\OvhSubscriber;

//Configuration
$config = array(
    'application_key'    => 'ApplicationKey',
    'application_secret' => 'ApplicationSecret',
    'consumer_key'       => 'ConsumerKey'
);

// Create a Guzzle client
$client new OvhClient(['base_url' => 'https://eu.api.ovh.com/1.0/, 'time_url' => 'auth/time');
// and add it the plugin
$client->getEmitter()->attach(new OvhSubscriber($config));

// Now the plugin will add the correct OVH headers to your guzzle request
$response = $client->get('/data')->send();
```

##Symfony 2 usage

```yml
parameters:
    ovh.baseurl: "https://eu.api.ovh.com/1.0/"
    ovh.timeurl: "auth/time"
    ovh.config:
        application_key: ApplicationKey
        application_secret: ApplicationSecret
        consumer_key: ConsumerKey

services:
    guzzle.ovh.client:
        class: Nafresne\GuzzleHttp\OvhClient
        arguments:
            - { base_url: %ovh.baseurl%, time_url: %ovh.timeurl%, emitter: @guzzle.ovh.emitter }

    guzzle.ovh.emitter:
        class: GuzzleHttp\Event\Emitter
        calls:
            - [attach, [@guzzle.ovh.subscriber]]

    guzzle.ovh.subscriber:
        class: Nafresne\GuzzleHttp\Subscriber\OvhSubscriber
        arguments: [%ovh.config%]
```
```php
$client = $this->container->get('guzzle.ovh.client');
$response = $client->get('hosting/web');
$body = $response->getBody();
```

##Tests

    composer install && vendor/bin/phpunit

##License

This plugin is licensed under the MIT License



