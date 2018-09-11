<?php

namespace OLBot\Console;


use OLBot\Category\AbstractCategory;
use OLBot\Logger;
use OLBot\Model\DB\AllowedUser;
use OLBot\Model\DB\Answer;
use OLBot\Util;
use Swagger\Client\Api\MessagesApi;
use Swagger\Client\Telegram\SendMessageBody;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendBirthdayReminders extends Command
{
    protected function configure()
    {
        $this
            ->setName('send:birthdayreminder')
            ->setDescription('will send birthday reminders to the botmaster');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = new MessagesApi();

        $users = AllowedUser::whereNotNull('birthday');

        foreach ($users as $user) {
            $years = Util::isUsersBirthdayToday($user);
            if (!is_null($years)) {
                $message = new SendMessageBody();
                $message->setChatId(self::$settings->botmasterId);
                $text = Answer::where(['category' => AbstractCategory::CAT_BIRTHDAY_REMINDER])->random()->text;
                Util::replacePlaceholders(['name' => $user->name, 'years' => $years], $text);
                $message->setText($text);
                try {
                    $api->sendMessage($message);
                    $output->writeln('sent birthday remoder for ' . $user->name . 'to botmaster');
                } catch (\Throwable $t) {
                    // TODO: "id_in" is effectively "id_out"
                    Logger::logError(self::$settings->botmasterId, $t);
                }
            }
        }
    }
}