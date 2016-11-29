<?php

namespace  Ricbra\Knmi\Exception;

class NoParamsFound extends \Exception
{
    public static function withResponse(string $response)
    {
        return new self(sprintf('No params found in response: %s', $response));
    }
}
