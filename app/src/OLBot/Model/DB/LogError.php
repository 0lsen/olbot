<?php

namespace OLBot\Model\DB;


class LogError extends Eloquent
{
    protected $table = 'log_error';
    protected $keyType = 'integer';
    protected $guarded = [];
}