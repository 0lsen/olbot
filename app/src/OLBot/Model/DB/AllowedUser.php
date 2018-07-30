<?php

namespace OLBot\Model\DB;


class AllowedUser extends Eloquent
{
    protected $table = 'allowed_users';
    protected $keyType = 'integer';
    protected $guarded = [];
}