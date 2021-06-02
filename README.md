[![Latest Stable Version](https://poser.pugx.org/ricbra/knmi-client/v/stable)](https://packagist.org/packages/ricbra/knmi-client)
[![Build status](https://travis-ci.org/ricbra/knmi-client.svg?branch=master)](https://travis-ci.org/ricbra/knmi-client)

# KNMI client

This repository contains a very simplistic PHP client for the KNMI [hourly](https://www.daggegevens.knmi.nl/klimatologie/uurgegevens) 
and [daily](https://www.daggegevens.knmi.nl/klimatologie/daggegevens) endpoints. The response from KNMI is rather
unsuable and this client returns a nice formatted array. Not the best you can get, but better than the original.

## Installation

Since we're using [php-http](http://docs.php-http.org/en/latest/) you're free to choose your own HTTP client library.
For more information on how to pick your favourite client see [the docs](http://docs.php-http.org/en/latest/httplug/users.html).

Installation using latest Guzzle:

    $ composer require php-http/guzzle6-adapter ricbra/knmi-client guzzlehttp/psr7 php-http/message

If you don't specify any php-http adapter Composer will complain:

    $ composer require ricbra/knmi-client
    ./composer.json has been created
    Loading composer repositories with package information
    Updating dependencies (including require-dev)
    Your requirements could not be resolved to an installable set of packages.

      Problem 1
        - ricbra/knmi-client 1.0.x-dev requires php-http/client-implementation ^1.0 -> no matching package found.
        - ricbra/knmi-client dev-master requires php-http/client-implementation ^1.0 -> no matching package found.
        - Installation request for ricbra/knmi-client @dev -> satisfiable by ricbra/knmi-client[dev-master, 1.0.x-dev].

## Usage

```php
<?php

require 'vendor/autoload.php';

$guzzleAdapter = new \Http\Adapter\Guzzle6\Client();
$client = new \Ricbra\Knmi\Client($guzzleAdapter);

$data = $client->getDaily(
    new \DateTime('2012-01-01'),
    new \DateTime('2012-01-03'),
    [
        '240'
    ],
    [
        'PX', 'PN'
    ]
);

$data = $response = $client->getHourly(
    new \DateTime('2016-01-01 12:00'),
    new \DateTime('2016-01-01 13:00'),
    ['240'],
    ['P']
);
```
