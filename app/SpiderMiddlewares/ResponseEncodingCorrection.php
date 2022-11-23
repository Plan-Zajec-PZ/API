<?php

namespace App\SpiderMiddlewares;

use RoachPHP\Downloader\Middleware\ResponseMiddlewareInterface;
use RoachPHP\Http\Response;
use RoachPHP\Support\Configurable;

class ResponseEncodingCorrection implements ResponseMiddlewareInterface
{
    use Configurable;

    public function handleResponse(Response $response): Response
    {
        $body = iconv('ISO-8859-2', 'UTF-8', $response->getBody());

        return $response->withBody($body);
    }
}
