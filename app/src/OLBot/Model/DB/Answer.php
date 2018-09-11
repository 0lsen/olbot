<?php

namespace OLBot\Model\DB;


class Answer extends Eloquent
{
    protected $table = 'answers';
    protected $keyType = 'integer';
    protected $guarded = [];
}