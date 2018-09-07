<?php

namespace OLBot\Model\DB;


class Keyword extends Eloquent
{
    protected $table = 'keywords';
    protected $keyType = 'string';
    protected $guarded = [];
}