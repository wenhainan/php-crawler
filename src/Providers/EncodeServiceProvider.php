<?php
/**
 * Created by PhpStorm.
 * User: Jaeger <JaegerCode@gmail.com>
 * Date: 2017/9/20
 */

namespace phpCrawler\Providers;

use phpCrawler\Contracts\ServiceProviderContract;
use phpCrawler\Kernel;
use phpCrawler\Services\EncodeService;

class EncodeServiceProvider implements ServiceProviderContract
{
    public function register(Kernel $kernel)
    {
        $kernel->bind('encoding',function (string $outputEncoding,string $inputEncoding = null){
            return EncodeService::convert($this,$outputEncoding,$inputEncoding);
        });
    }
}