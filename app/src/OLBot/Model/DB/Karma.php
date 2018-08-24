<?php

namespace OLBot\Model\DB;


class Karma extends Eloquent
{
    protected $table = 'karma';
    protected $keyType = 'integer';
    protected $guarded = [];
}