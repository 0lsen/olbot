<?php

namespace OLBot\Console;


use OLBot\Logger;
use OLBot\Settings;
use Swagger\Client\Api\MessagesApi;
use Swagger\Client\Telegram\SendMessageBody;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends \Symfony\Component\Console\Command\Command
{
    /** @var Settings */
    public static $settings;
    /** @var MessagesApi */
    public static $api;

    protected function sendTextMessage($chatId, $text, OutputInterface $output)
    {
        $message = new SendMessageBody();
        $message->setChatId($chatId);
        $message->setReplyToMessageId(0);
        $message->setText($text);

        try {
            self::$api->sendMessage(self::$settings->token, $message);
            Logger::logMessageOut($message);
            $output->writeln('text sent to ' . $chatId . ': ' . $text);
        } catch (\Throwable $t) {
            // TODO: id_in is essentially id_out here...
            Logger::logError($chatId, $t);
            $output->writeln('Error: ' . $t->getMessage());
        }
    }
}