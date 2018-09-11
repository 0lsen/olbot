<?php

namespace OLBot\Console;


use OLBot\Category\AbstractCategory;
use OLBot\Logger;
use OLBot\Model\DB\AllowedUser;
use OLBot\Model\DB\Answer;
use OLBot\Model\DB\GroupUser;
use OLBot\Util;
use Swagger\Client\Api\MessagesApi;
use Swagger\Client\Telegram\SendMessageBody;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendBirthdayGreetings extends Command
{
    protected function configure()
    {
        $this
            ->setName('send:birthdaysgreetings')
            ->setDescription('will send happy birthdays to known group members');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $api = new MessagesApi();

        $users = AllowedUser::whereNotNull('birthday');

        foreach ($users as $user) {
            $years = Util::isUsersBirthdayToday($user);
            if (!is_null($years)) {
                $groups = GroupUser::where(['user' => $user->id]);
                foreach ($groups as $group) {
                    $message = new SendMessageBody();
                    $message->setChatId($group->group);
                    $text = Answer::where(['category' => AbstractCategory::CAT_BIRTHDAY_GREETING])->random()->text;
                    Util::replacePlaceholders(['name' => $user->name, 'years' => $years], $text);
                    $message->setText($text);
                    try {
                        $api->sendMessage($message);
                        $output->writeln('sent birthday greeting for '.$user->name.' in '.$group->group);
                    } catch (\Throwable $t) {
                        // TODO: "id_in" is effectively "id_out"
                        Logger::logError($group->group, $t);
                    }
                }
            }
        }
    }
}