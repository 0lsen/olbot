<?php

namespace OLBot\Model\DB;


class AllowedGroup extends Eloquent
{
    protected $table = 'allowed_groups';
    protected $keyType = 'integer';
    protected $guarded = [];
}