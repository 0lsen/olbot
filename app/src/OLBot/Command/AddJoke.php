<?php

namespace OLBot\Command;


use OLBot\Service\StorageService;

class AddJoke extends AbstractCommand
{
    public function __construct(StorageService $storageService, array $settings = [])
    {
        parent::__construct($storageService, array_merge($settings, ['numberOfArguments' => 1]));
    }

    public function doStuff()
    {
        parent::doStuff();
        $this->tryToAddNewText('Joke');
    }
}