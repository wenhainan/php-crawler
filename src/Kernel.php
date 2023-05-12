<?php
/**
 * Created by PhpStorm.
 * User: Jaeger <JaegerCode@gmail.com>
 * Date: 2017/9/21
 */

namespace phpCrawler;

use phpCrawler\Contracts\ServiceProviderContract;
use phpCrawler\Exceptions\ServiceNotFoundException;
use phpCrawler\Providers\EncodeServiceProvider;
use Closure;
use phpCrawler\Providers\HttpServiceProvider;
use phpCrawler\Providers\PluginServiceProvider;
use phpCrawler\Providers\SystemServiceProvider;
use Tightenco\Collect\Support\Collection;

class Kernel
{
    protected $providers = [
        SystemServiceProvider::class,
        HttpServiceProvider::class,
        EncodeServiceProvider::class,
        PluginServiceProvider::class
    ];

    protected $binds;
    protected $ql;

    /**
     * Kernel constructor.
     * @param $ql
     */
    public function __construct(QueryList $ql)
    {
        $this->ql = $ql;
        $this->binds = new Collection();
    }

    public function bootstrap()
    {
        //注册服务提供者
        $this->registerProviders();
        return $this;
    }

    public function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }
    }

    public function bind(string $name,Closure $provider)
    {
        $this->binds[$name] = $provider;
    }

    public function getService(string $name)
    {
        if(!$this->binds->offsetExists($name)){
            throw new ServiceNotFoundException("Service: {$name} not found!");
        }
        return $this->binds[$name];
    }

    private function register(ServiceProviderContract $instance)
    {
        $instance->register($this);
    }


}