<?php

namespace OLBot\Command;


use OLBot\Service\StorageService;

class AddCategoryAnswer extends AbstractCommand
{
    private $category;

    public function __construct(StorageService $storageService, array $settings = [])
    {
        if (!isset($settings['category'])) {
            throw new \Exception('no category setting in AddCategoryAnwser constructor');
        }
        $this->category = $settings['category'];
        parent::__construct($storageService, $settings);
    }

    public function doStuff()
    {
        parent::doStuff();
        $this->tryToAddNewText('Answer', ['category' => $this->category]);
    }
}