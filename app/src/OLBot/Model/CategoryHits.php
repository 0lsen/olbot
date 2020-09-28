<?php

namespace OLBot\Model;


use OLBotSettings\Model\CategorySettings;

class CategoryHits
{
    private $id;
    private $category;
    private $settings;
    private $hits = [];

    public function __construct($id, string $category, ?CategorySettings $settings)
    {
        $this->id = $id;
        $this->category = $category;
        $this->settings = $settings;
    }

    public function addHit(string $word) {
        $this->hits[] = $word;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @return CategorySettings|null
     */
    public function getSettings(): ?CategorySettings
    {
        return $this->settings;
    }

    /**
     * @return string[]
     */
    public function getHits(): array
    {
        return $this->hits;
    }

    public static function cmp(CategoryHits $ch1, CategoryHits $ch2)
    {
        return $ch2->hits <=> $ch1->hits;
    }
}