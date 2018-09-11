<?php

require __DIR__ . '/../vendor/autoload.php';

$dbSettings = require __DIR__ . '/config/database.php';
$botSettings = require __DIR__ . '/config/olbot.php';

$connection = new \Illuminate\Database\Capsule\Manager();
$connection->addConnection($dbSettings);
$connection->bootEloquent();

\OLBot\Console\Command::$settings = $botSettings;

$app = new \Symfony\Component\Console\Application();
$app->add(new \OLBot\Console\SendBirthdayGreetings());
$app->add(new \OLBot\Console\SendBirthdayReminders());
$app->add(new \OLBot\Console\TeachKeywords());

$app->run();