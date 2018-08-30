<?php

namespace OLBot\Model\DB;


class LogMessageOut extends Eloquent
{
    protected $table = 'log_message_out';
    protected $keyType = 'integer';
    protected $guarded = [];
}