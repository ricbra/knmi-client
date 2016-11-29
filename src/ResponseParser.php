<?php

namespace Ricbra\Knmi;

use Ricbra\Knmi\Exception\NoParamsFound;

class ResponseParser
{
    const HOURLY_RESPONSE = 'HH       = tijd';

    public function parse(string $body) : array
    {
        $lines      = explode("\n", $body);
        $stations   = $this->parseStations($body);
        $params     = $this->parseParams($body);
        $response   = [];
        $isHourly   = strpos($body, self::HOURLY_RESPONSE) !== false;

        foreach ($lines as $line) {
            if (strpos($line, '#') === false) {
                $arr = explode(',', $line);

                if (count($arr) < 3) {
                    continue;
                }

                array_walk(
                    $arr,
                    function (&$v) {
                        $v = trim($v);
                    }
                );

                $station = $arr[0];
                if ($isHourly) {
                    $date = \DateTime::createFromFormat('Ymd', $arr[1], new \DateTimeZone('Europe/Amsterdam'));
                    $date->setTime($arr[2], 0, 0);
                    $ret['datetime'] = $date->format(\DateTime::ISO8601);
                } else {
                    $date = \DateTime::createFromFormat('Ymd', $arr[1], new \DateTimeZone('Europe/Amsterdam'));
                    $ret['date'] = $date->format('Y-m-d');
                }
                $values = array_splice($arr, $isHourly ? 3 : 2);
                $ret['station'] = $stations[$station];
                $ret['data'] = array();
                foreach ($params as $index => $k) {
                    $ret['data'][$k] = $values[$index];
                }
                $response[] = $ret;
            }
        }

        return $response;
    }

    public function parseStations(string $body) : array
    {
        $lines      = explode("\r\n", $body);
        $stations   = [];

        foreach ($lines as $line) {
            if (preg_match(
                '/# ([0-9]{3}):\s+([0-9]+.[0-9]+)\s+([0-9]+.[0-9]+)\s+(-?[0-9]+.[0-9]+)\s+([A-Z\s\(\)]+)/',
                $line,
                $matches
            )) {
                $stations[$matches[1]] = [
                    'number' => $matches[1],
                    'lng' => $matches[2],
                    'lat' =>  $matches[3],
                    'alt' => $matches[4],
                    'name' => trim($matches[5])
                ];
            }
        }

        return $stations;
    }

    public function parseParams(string $body) : array
    {
        $lines = explode("\n", $body);
        $isHourly   = strpos($body, self::HOURLY_RESPONSE) !== false;

        foreach ($lines as $line) {
            if (preg_match('/# STN,YYYYMMDD,/', $line, $matches)) {
                $arr = explode(',', $line);
                array_walk(
                    $arr,
                    function (&$v) {
                        $v = trim($v);
                    }
                );

                return array_slice($arr, $isHourly ? 3 : 2);
            }
        }

        throw NoParamsFound::withResponse($body);
    }
}
