<?php

namespace OLBot\Model\DB;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Eloquent
 * @package OLBot\Model\DB
 * @method static Builder where(array|string $array, string $operator=null, mixed  $value=null)
 * @method static Eloquent create(array $array)
 * @method static Eloquent find(string $id)
 * @method static Collection whereNotNull(string $field)
 * @method static Eloquent firstOrCreate(array $array)
 */
abstract class Eloquent extends Model
{
    public $timestamps = false;
}