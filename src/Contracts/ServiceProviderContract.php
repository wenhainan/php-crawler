<?php
/**
 * Created by PhpStorm.
 * User: Jaeger <JaegerCode@gmail.com>
 * Date: 2017/9/20
 */

namespace phpCrawler\Contracts;

use phpCrawler\Kernel;

interface ServiceProviderContract
{
    public function register(Kernel $kernel);
}