<?php


namespace OLBot\Command;


use OLBot\Service\StorageService;

abstract class StorageCommand implements CommandInterface {

    /** @var StorageService */
    protected static $storageService;

    /**
     * @param StorageService $storageService
     */
    public static function setStorageService(StorageService $storageService): void
    {
        self::$storageService = $storageService;
    }
}