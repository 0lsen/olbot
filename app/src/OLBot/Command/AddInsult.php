<?php

namespace OLBot\Command;


class AddInsult extends AbstractCommand
{
    public function __construct(array $settings = [])
    {
        parent::__construct(array_merge(['numberOfArguments' => 1], $settings));
    }

    public function doStuff()
    {
        parent::doStuff();
        $this->tryToAddNewText('Karma', ['karma' => false]);
    }
}