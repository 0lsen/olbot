<?php


namespace OLBot\Model\DB;


class Category extends Eloquent
{
    protected $table = 'categories';
    protected $keyType = 'integer';
    protected $guarded = [];
}