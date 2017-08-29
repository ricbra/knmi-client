<?php

namespace Test\Ricbra\Knmi;

use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Http\Discovery\MessageFactoryDiscovery;
use Http\Mock\Client as MockClient;
use Ricbra\Knmi\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_calls_knmi_with_correct_params()
    {
        $mockClient = new MockClient();
        $mockClient->addResponse(
            MessageFactoryDiscovery::find()->createResponse(
                200,
                null,
                [],
                file_get_contents(__DIR__ . '/../resources/getdata_hourly')
            )
        );

        $client = new Client($mockClient);
        $client->getDaily(
            new \DateTime('2016-01-01'),
            new \DateTime('2016-01-05'),
            ['240', '375', '343'],
            ['PX', 'PN']
        );

        $request = $mockClient->getRequests()[0];

        $this->assertSame(
            'start=20160101&end=20160105&stns=240%3A375%3A343&vars=PX%3APN',
            $request->getBody()->getContents()
        );

        $this->assertSame(
            'http://projects.knmi.nl/klimatologie/daggegevens/getdata_dag.cgi',
            (string)$request->getUri()
        );

        $this->assertSame(
            'POST',
            $request->getMethod()
        );
    }

    /**
     * @group integration
     * @test
     */
    public function it_correctly_calls_and_parses_knmi_daily_response()
    {
        $guzzleClient = new GuzzleClient();
        $guzzleAdapter = new GuzzleAdapter($guzzleClient);
        $client = new Client($guzzleAdapter);
        $response = $client->getDaily(
            new \DateTime('2016-01-01'),
            new \DateTime('2016-01-01'),
            ['240'],
            ['PX', 'PN']
        );

        $this->assertSame([
            'date'    => '2016-01-01',
            'station' =>
                [
                    'number' => '240',
                    'lng'    => '4.790',
                    'lat'    => '52.318',
                    'alt'    => '-3.30',
                    'name'   => 'SCHIPHOL',
                ],
            'data'    =>
                [
                    'PX' => '10251',
                    'PN' => '10146',
                ],
        ], $response[0]);
    }

    /**
     * @group integration
     * @test
     */
    public function it_correctly_calls_and_parses_knmi_hourly_response()
    {
        $guzzleClient = new GuzzleClient();
        $guzzleAdapter = new GuzzleAdapter($guzzleClient);
        $client = new Client($guzzleAdapter);
        $response = $client->getHourly(
            new \DateTime('2016-01-01'),
            new \DateTime('2016-01-01'),
            12,
            13,
            ['240'],
            ['P']
        );
        $this->assertSame(
            [
                [
                    'datetime' => '2016-01-01T12:00:00+0100',
                    'station'  =>
                        [
                            'number' => '240',
                            'lng'    => '4.790',
                            'lat'    => '52.318',
                            'alt'    => '-3.30',
                            'name'   => 'SCHIPHOL',
                        ],
                    'data'     =>
                        [
                            'P' => '10242',
                        ],
                ],
                [
                    'datetime' => '2016-01-01T13:00:00+0100',
                    'station'  =>
                        [
                            'number' => '240',
                            'lng'    => '4.790',
                            'lat'    => '52.318',
                            'alt'    => '-3.30',
                            'name'   => 'SCHIPHOL',
                        ],
                    'data'     =>
                        [
                            'P' => '10236',
                        ],
                ],
            ],
            $response
        );
    }
    /**
     * @group integration
     * @test
     */
    public function it_correctly_calls_and_parses_station_names_with_special_characters()
    {
        $guzzleClient = new GuzzleClient();
        $guzzleAdapter = new GuzzleAdapter($guzzleClient);
        $client = new Client($guzzleAdapter);
        $response = $client->getHourly(
            new \DateTime('2016-01-01'),
            new \DateTime('2016-01-01'),
            12,
            13,
            ['343'],
            ['P']
        );
        $this->assertSame(
            "R'DAM-GEULHAVEN",
            $response[0]['station']['name']
        );
    }
}
