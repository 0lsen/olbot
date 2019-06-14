<?php

namespace OLBot\Command;


use OLBot\Category\AbstractCategory;

class CategoryAnswer extends AbstractCommand
{
    private $category;
    private $type;

    public function __construct(array $settings = [])
    {
        if (!isset($settings['category'])) {
            throw new \Exception('no category setting in CategoryAnwser constructor');
        }
        $this->category = $settings['category'];
        $this->type = $settings['type'] ?? 'text';
        parent::__construct(array_merge(['numberOfArguments' => 0], $settings));
    }

    public function doStuff()
    {
        if (empty(self::$storageService->textCopy)) {
            $this->createCategoryResponse();
        } else {
            parent::doStuff();
            if ($this->type == 'pic' && !preg_match('#^https?://.+$#', self::$storageService->textCopy)) {
                self::$storageService->response->text[] = $this->replyToInvalidInput;
            } else {
                $this->tryToAddNewText('Answer', ['category' => $this->category]);
            }
        }
    }

    private function createCategoryResponse() {
        $className = '\OLBot\Category\\'.self::$storageService->settings->parser->categories[$this->category]['class'];
        /** @var AbstractCategory $category */
        $category = new $className($this->category, null, $this->storageService->settings->parser->categories[$this->category]['settings'] ?? []);
        $category->generateResponse();
    }
}