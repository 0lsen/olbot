<?php

namespace OLBot\Model;


class CategoryHits
{
    public $category;
    public $hits = 0;

    public function __construct(string $category)
    {
        $this->category = $category;
    }

    public static function cmp(CategoryHits $ch1, CategoryHits $ch2)
    {
        return $ch2->hits <=> $ch1->hits;
    }
}