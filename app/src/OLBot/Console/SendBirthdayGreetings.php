<?php

namespace OLBot\Console;


use OLBot\Category\AbstractCategory;
use OLBot\Model\DB\AllowedUser;
use OLBot\Model\DB\Answer;
use OLBot\Model\DB\GroupUser;
use OLBot\Util;
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
        $users = AllowedUser::whereNotNull('birthday');

        foreach ($users as $user) {
            $years = Util::isUsersBirthdayToday($user);
            if (!is_null($years)) {
                $groups = GroupUser::where(['user' => $user->id]);
                foreach ($groups as $group) {
                    $texts = Answer::where(['category' => AbstractCategory::CAT_BIRTHDAY_GREETING]);
                    if (!$texts->count()) {
                        $output->writeln('no birthday greeting answers found.');
                        return;
                    }
                    $text = $texts->inRandomOrder()->first()->text;
                    Util::replacePlaceholders(['name' => $user->name, 'years' => $years], $text);
                    $this->sendTextMessage($group->group, $text, $output);
                }
            }
        }
    }
}