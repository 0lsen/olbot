<?php

namespace OLBot\Command;


use OLBot\Category\AbstractCategory;
use OLBotSettings\Model\CategoryAnswer as CategoryAnswerSettings;

class CategoryAnswer extends AbstractCommand
{
    private $category;
    private $type;

    /**
     * CategoryAnswer constructor.
     * @param CategoryAnswerSettings $settings
     * @throws \Exception
     */
    public function __construct(CategoryAnswerSettings $settings)
    {
        if (is_null($settings->getCategory())) {
            throw new \Exception('no category setting in CategoryAnwser constructor');
        }
        $this->category = $settings->getCategory();
        $this->type = $settings->getContentType() ?? \OLBotSettings\Model\CategoryAnswer::CONTENT_TYPE_TEXT;
        parent::__construct($settings);
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

    private function createCategoryResponse()
    {
        foreach (self::$storageService->settings->getParser()->getCategories() as $category) {
            if ($category->getCategoryNumber() === $this->category) {
                $className = '\OLBot\Category\\'.$category->getType();
                /** @var AbstractCategory $categoryObject */
                $categoryObject = new $className($this->category, null, $category);
                $categoryObject->generateResponse();
                break;
            }
        }
    }
}