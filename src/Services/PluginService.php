<?php
/**
 * Created by PhpStorm.
 * User: Jaeger <JaegerCode@gmail.com>
 * Date: 2017/9/22
 */

namespace phpCrawler\Services;

use phpCrawler\QueryList;

class PluginService
{
    public static function install(QueryList $queryList, $plugins, ...$opt)
    {
        if(is_array($plugins))
        {
            foreach ($plugins as $plugin) {
                $plugin::install($queryList);
            }
        }else{
            $plugins::install($queryList,...$opt);
        }
        return $queryList;
    }
}