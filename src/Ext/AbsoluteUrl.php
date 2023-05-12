<?php
/**
 * Created by PhpStorm.
 * User: Jaeger <JaegerCode@gmail.com>
 * Date: 2017/9/26
 * Converting relative urls to absolute
 */

namespace phpCrawler\Ext;

use phpCrawler\Contracts\PluginContract;
use phpCrawler\QueryList;
use phpCrawler\Ext\phpUri;

class AbsoluteUrl implements PluginContract
{
    public static function install(QueryList $queryList, ...$opt)
    {
        $absoluteUrl = $opt[0] ?? 'absoluteUrl';
        $absoluteUrlHelper = $opt[1] ?? 'absoluteUrlHelper';

        // convert all link
        $queryList->bind($absoluteUrl,function ($url){
            return AbsoluteUrl::convertAll($this,$url);
        });

        // convert helper
        $queryList->bind($absoluteUrlHelper,function ($url,$relativeUrl){
            return phpUri::parse($url)->join($relativeUrl);
        });
    }

    public static function convertAll($ql,$url)
    {
        $parser = phpUri::parse($url);
        $ql->find('a')->map(function($item) use ($parser,$ql){
            $relativeUrl = $item->attr('href');
            $absoluteUrl = $parser->join($relativeUrl);
            $item->attr('href',$absoluteUrl);
        });
        $ql->find('img')->map(function($item) use ($parser,$ql){
            $relativeUrl = $item->attr('src');
            $absoluteUrl = $parser->join($relativeUrl);
            $item->attr('src',$absoluteUrl);
        });
       $ql->setHtml($ql->find('')->html());
        return $ql;
    }
}