<?php

namespace OLBot\Console;


use OLBot\Category\AbstractCategory;
use OLBot\Model\DB\AllowedUser;
use OLBot\Model\DB\Answer;
use OLBot\Util;
use Swagger\Client\Api\MessagesApi;
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
                $text = Answer::where(['category' => AbstractCategory::CAT_BIRTHDAY_REMINDER])->inRandomOrder()->first()->text;
                Util::replacePlaceholders(['name' => $user->name, 'years' => $years], $text);
                $this->sendTextMessage(self::$settings->botmasterId, $text, $output);
            }
        }
    }
}