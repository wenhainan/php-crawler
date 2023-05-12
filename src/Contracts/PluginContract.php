<?php
/**
 * Created by PhpStorm.
 * User: Jaeger <JaegerCode@gmail.com>
 * Date: 2017/9/22
 */

namespace phpCrawler\Contracts;

use phpCrawler\QueryList;

interface PluginContract
{
    public static function install(QueryList $queryList,...$opt);
}