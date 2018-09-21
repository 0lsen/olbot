<?php

namespace OLBot\Model;


class CategoryHits
{
    public $id;
    public $category;
    public $settings;
    public $hits = 0;

    public function __construct($id, string $category, $settings = [])
    {
        $this->id = $id;
        $this->category = $category;
        $this->settings = $settings;
    }

    public static function cmp(CategoryHits $ch1, CategoryHits $ch2)
    {
        return $ch2->hits <=> $ch1->hits;
    }
}