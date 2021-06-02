<?php
declare(strict_types=1);

namespace Ricbra\Knmi;

use Http\Client\HttpClient;
use Http\Discovery\MessageFactoryDiscovery;

class Client
{
    private $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function getDaily(\DateTime $start, \DateTime $end, array $stations, array $vars) : array
    {
        $factory = MessageFactoryDiscovery::find();
        $params = [
            'start' => $start->format('Ymd'),
            'end' => $end->format('Ymd'),
            'stns' => implode(':', $stations),
            'vars' => implode(':', $vars)
        ];
        $request = $factory->createRequest(
            'POST',
            'https://www.daggegevens.knmi.nl/klimatologie/daggegevens',
            [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            http_build_query($params)
        );

        return (new ResponseParser())->parse($this->httpClient->sendRequest($request)->getBody()->getContents());
    }

    public function getHourly(\DateTime $start, \DateTime $end, $timeframeStart, $timeframeEnd, array $stations, array $vars) : array
    {

        $start = $start->format('Ymd') . sprintf("%02d", $timeframeStart);
        $end = $end->format('Ymd') . sprintf("%02d", $timeframeEnd);

        $factory = MessageFactoryDiscovery::find();
        $params = [
            'start' => $start,
            'end' => $end,
            'stns' => implode(':', $stations),
            'vars' => implode(':', $vars)
        ];
        $request = $factory->createRequest(
            'POST',
            'https://www.daggegevens.knmi.nl/klimatologie/uurgegevens',
            [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            http_build_query($params)
        );

        return (new ResponseParser())->parse($this->httpClient->sendRequest($request)->getBody()->getContents());
    }
}
