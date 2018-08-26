<?php

namespace OLBot\Model\DB;


class Joke extends Eloquent
{
    protected $table = 'jokes';
    protected $keyType = 'integer';
    protected $guarded = [];
}