<?php

namespace OLBot\Middleware;


use OLBot\Service\CacheService;
use OLBot\Service\StorageService;

abstract class TextBasedMiddleware
{
    /** @var StorageService */
    protected $storageService;

    /** @var CacheService */
    protected $cacheService;

    public function __construct(StorageService $storageService, CacheService $cacheService)
    {
        $this->storageService = $storageService;
        $this->cacheService = $cacheService;
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