<?php

namespace OLBot\Console;


use OLBot\Category\AbstractCategory;
use OLBot\Model\DB\Answer;
use OLBot\Model\DB\LogError;
use OLBot\Util;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ErrorReport extends Command
{
    protected function configure()
    {
        $this
            ->setName('report:errors')
            ->setDescription('reports no. of logged errors in the last 24h');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $noOfErrors = LogError::where('time', '>', time()-(24*60*60))->count();

        if ($noOfErrors > 0) {
            $texts = Answer::where(['category' => AbstractCategory::CAT_ERROR_REPORT]);
            $text = $texts->count() ? $texts->inRandomOrder()->first()->text : '#number# errors recorded.';
            Util::replacePlaceholders(['number' => $noOfErrors], $text);
            $this->sendTextMessage(self::$settings->botmasterId, $text, $output);
        }
    }
}