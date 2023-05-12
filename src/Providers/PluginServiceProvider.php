<?php
/**
 * Created by PhpStorm.
 * User: Jaeger <JaegerCode@gmail.com>
 * Date: 2017/9/22
 */

namespace phpCrawler\Providers;

use phpCrawler\Contracts\ServiceProviderContract;
use phpCrawler\Kernel;
use phpCrawler\Services\PluginService;

class PluginServiceProvider implements ServiceProviderContract
{
    public function register(Kernel $kernel)
    {
        $kernel->bind('use',function ($plugins,...$opt){
            return PluginService::install($this,$plugins,...$opt);
        });
    }

}