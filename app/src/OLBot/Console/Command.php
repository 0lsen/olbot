<?php

namespace OLBot\Console;


use OLBot\Settings;

abstract class Command extends \Symfony\Component\Console\Command\Command
{
    /** @var Settings */
    public static $settings;
}