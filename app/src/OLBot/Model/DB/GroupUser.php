<?php

namespace OLBot\Model\DB;


class GroupUser extends Eloquent
{
    protected $table = 'group_users';
    protected $keyType = 'integer';
    protected $guarded = [];
}