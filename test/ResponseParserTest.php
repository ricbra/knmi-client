<?php

namespace Test\Ricbra\Knmi;

use Ricbra\Knmi\ResponseParser;

class ResponseParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_parses_daily_data()
    {
        $response = file_get_contents(__DIR__ . '/../resources/getdata_dag');

        $parser = new ResponseParser();
        $parsed = $parser->parse($response);

        $this->assertSame(
            [
                [
                    'date'    => '2001-10-01',
                    'station' =>
                        [
                            'number' => '235',
                            'lng'    => '4.781',
                            'lat'    => '52.928',
                            'alt'    => '1.20',
                            'name'   => 'DE KOOY',
                        ],
                    'data'    =>
                        [
                            'PX'  => '10039',
                            'PXH' => '24',
                            'RH'  => '10',
                        ],
                ],
            ],
            $parsed
        );
    }

    /**
     * @test
     */
    public function it_parses_hourly_data()
    {
        $response = file_get_contents(__DIR__ . '/../resources/getdata_hourly');

        $parser = new ResponseParser();
        $parsed = $parser->parse($response);
        $this->assertSame(
            [
                [
                    'datetime' => '2016-01-01T12:00:00+0100',
                    'station'  =>
                        [
                            'number' => '240',
                            'lng'    => '4.774',
                            'lat'    => '52.301',
                            'alt'    => '-4.40',
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
                            'lng'    => '4.774',
                            'lat'    => '52.301',
                            'alt'    => '-4.40',
                            'name'   => 'SCHIPHOL',
                        ],
                    'data'     =>
                        [
                            'P' => '10236',
                        ],
                ],
            ],
            $parsed
        );
    }

    /**
     * @test
     */
    public function it_parses_station_names_with_special_characters()
    {
        $response = file_get_contents(__DIR__ . '/../resources/getdata_dag_special_characters_name');

        $parser = new ResponseParser();
        $parsed = $parser->parse($response);

        $this->assertSame(
            "R'DAM-GEULHAVEN",
            $parsed[0]['station']['name']
        );
    }
}
