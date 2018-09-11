<?php

namespace OLBot\Console;


use OLBot\Model\DB\Keyword;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TeachKeywords extends Command
{
    protected function configure()
    {
        $this
            ->setName('teach:keywords')
            ->setDescription('will fill keyword table')
            ->addArgument('file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = $input->getArgument('file');
        if (!file_exists($file)) {
            $output->writeln('invalid file path');
        } else {
            try {
                // for now expecting a php file returning ['keyword1'=>#cat1, 'keyword2'=>#cat2, ...]
                $keywords = require $file;
                foreach ($keywords as $keyword => $category) {
                    Keyword::firstOrCreate([
                        'id' => md5($keyword),
                        'category' => $category
                    ]);
                }
            } catch (\Throwable $t) {
                $output->writeln('something went wrong: ' . $t->getMessage());
            }
        }
    }
}