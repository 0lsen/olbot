<?php

namespace OLBot\Settings;


class InstantResponseSettings
{
    public $regex;
    public $response;
    public $break;

    public function __construct(string $regex, string $response, bool $break = false)
    {
        $this->regex = $regex;
        $this->response = $response;
        $this->break = $break;
    }
}