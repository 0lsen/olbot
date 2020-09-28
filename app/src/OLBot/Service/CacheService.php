<?php


namespace OLBot\Service;


use OLBotSettings\Model\CacheSettings;

class CacheService
{
    /** @var bool */
    private $active;
    /** @var string */
    private $method;
    /** @var int */
    private $ttl;

    /**
     * CacheService constructor.
     * @param bool $active
     * @param string $method
     * @param int $ttl
     */
    public function __construct(CacheSettings $settings)
    {
        $this->active = $settings->getActive();
        $this->method = $settings->getMethod();
        $this->ttl = $settings->getTtl();
    }


    public function fetch(string $key)
    {
        if (!$this->active) {
            return null;
        }
        switch ($this->method) {
            case 'apcu':
                $item = apcu_fetch($key);
                break;
            case 'tmp':
                // TODO: respect ttl
                $tmp = file_get_contents(sys_get_temp_dir().'/'.$key);
                $item = $tmp ? unserialize($tmp) : null;
                break;
            default:
                $item = null;
        }
        return $item;
    }

    public function store(string $key, $value)
    {
        if (!$this->active) {
            return;
        }
        switch ($this->method) {
            case 'apcu':
                apcu_store($key, $value, $this->ttl);
                break;
            case 'tmp':
                file_put_contents(sys_get_temp_dir().'/'.$key, serialize($value));
                break;
        }
    }
}