<?php


namespace App\Http;


use Slim\Http\Headers;
use Slim\Http\Stream;

class Response extends \Slim\Http\Response
{
    public static function createFromOAuth(\OAuth2\Response $oa_response)
    {
        // Take care of headers
        $oa_headers = $oa_response->getHttpHeaders();
        $headers = new Headers();
        foreach ($oa_headers as $key => $value) {
            $headers->add($key, $value);
        }

        // Create stream for body content
        $stream = fopen('php://memory','r+');
        fwrite($stream, $oa_response->getResponseBody());
        rewind($stream);

        return new static(
            $oa_response->getStatusCode(),
            $headers,
            new Stream($stream)
        );
    }
}