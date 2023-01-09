<?php

namespace App\SpiderParsers;

use RoachPHP\Http\Response;

class Parser
{
    public function __construct(
        protected Response $response,
    ){
    }

}
