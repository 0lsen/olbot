<?php

namespace OLBot\Middleware;


use OLBot\Service\StorageService;

abstract class TextBasedMiddleware
{
    /** @var StorageService */
    protected $storageService;

    public function __construct($storageService)
    {
        $this->storageService = $storageService;
    }

    protected function removeFromText($needle, $all = false)
    {
        $text = $this->storageService->message->getText();

        if ($all) {
            $text = str_replace($needle, '', $text);
        } else {
            $pos = strpos($this->storageService->message->getText(), $needle);
            $text = substr_replace($text, '', $pos, strlen($needle));
        }

        $this->storageService->message->setText($text);
    }
}