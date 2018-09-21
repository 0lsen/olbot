<?php

namespace OLBot\Command;


use OLBot\Service\StorageService;

class AddCategoryAnswer extends AbstractCommand
{
    private $category;
    private $type;

    public function __construct(StorageService $storageService, array $settings = [])
    {
        if (!isset($settings['category'])) {
            throw new \Exception('no category setting in AddCategoryAnwser constructor');
        }
        $this->category = $settings['category'];
        $this->type = $settings['type'] ?? 'text';
        parent::__construct($storageService, $settings);
    }

    public function doStuff()
    {
        parent::doStuff();
        if ($this->type == 'pic' && !preg_match('#^https?://.+$#', $this->storageService->textCopy)) {
            $this->storageService->response->text[] = $this->replyToInvalidInput;
        } else {
            $this->tryToAddNewText('Answer', ['category' => $this->category]);
        }
    }
}