# phpCrawler 
`phpCrawler` is a simple, elegant, extensible PHP  Web Scraper (crawler/spider) ,based on phpQuery.

[API Documentation](https://github.com/wenhainan/phpCrawler) 

[中文文档](README-ZH.md)

## Features
- Have the same CSS3 DOM selector as jQuery
- Have the same DOM manipulation API as jQuery
- Have a generic list crawling program
- Have a strong HTTP request suite, easy to achieve such as: simulated landing, forged browser, HTTP proxy and other complex network requests
- Have a messy code solution
- Have powerful content filtering, you can use the jQuey selector to filter content
- Has a high degree of modular design, scalability and strong
- Have an expressive API
- Has a wealth of plug-ins

Through plug-ins you can easily implement things like:
- Multithreaded crawl
- Crawl JavaScript dynamic rendering page (PhantomJS/headless WebKit)
- Image downloads to local
- Simulate browser behavior such as submitting Form forms
- Web crawler
- .....

## Requirements
- PHP >= 7.1

## Installation
By Composer installation:
```
composer require wenhainan/php-crawler
```

## Usage

#### DOM Traversal and Manipulation
-  Crawl「GitHub」all picture links

```php
phpCrawler::get('https://github.com')->find('img')->attrs('src');
```
- Crawl Google search results

```php
$ql = phpCrawler::get('https://www.google.co.jp/search?q=phpCrawler');

$ql->find('title')->text(); //The page title
$ql->find('meta[name=keywords]')->content; //The page keywords

$ql->find('h3>a')->texts(); //Get a list of search results titles
$ql->find('h3>a')->attrs('href'); //Get a list of search results links

$ql->find('img')->src; //Gets the link address of the first image
$ql->find('img:eq(1)')->src; //Gets the link address of the second image
$ql->find('img')->eq(2)->src; //Gets the link address of the third image
// Loop all the images
$ql->find('img')->map(function($img){
	echo $img->alt;  //Print the alt attribute of the image
});
```
- More usage

```php
$ql->find('#head')->append('<div>Append content</div>')->find('div')->htmls();
$ql->find('.two')->children('img')->attrs('alt'); // Get the class is the "two" element under all img child nodes
// Loop class is the "two" element under all child nodes
$data = $ql->find('.two')->children()->map(function ($item){
    // Use "is" to determine the node type
    if($item->is('a')){
        return $item->text();
    }elseif($item->is('img'))
    {
        return $item->alt;
    }
});

$ql->find('a')->attr('href', 'newVal')->removeClass('className')->html('newHtml')->...
$ql->find('div > p')->add('div > ul')->filter(':has(a)')->find('p:first')->nextAll()->andSelf()->...
$ql->find('div.old')->replaceWith( $ql->find('div.new')->clone())->appendTo('.trash')->prepend('Deleted')->...
```
#### List crawl
Crawl the title and link of the Google search results list:
```php
$data = phpCrawler::get('https://www.google.co.jp/search?q=phpCrawler')
	// Set the crawl rules
    ->rules([ 
	    'title'=>array('h3','text'),
	    'link'=>array('h3>a','href')
	])
	->query()->getData();

print_r($data->all());
```
 Results:
```
Array
(
    [0] => Array
        (
            [title] => Angular - phpCrawler
            [link] => https://angular.io/api/core/phpCrawler
        )
    [1] => Array
        (
            [title] => phpCrawler | @angular/core - Angularリファレンス - Web Creative Park
            [link] => http://www.webcreativepark.net/angular/phpCrawler/
        )
    [2] => Array
        (
            [title] => phpCrawlerにQueryを追加したり、追加されたことを感知する | TIPS ...
            [link] => http://www.webcreativepark.net/angular/phpCrawler_query_add_subscribe/
        )
        //...
)
```
#### Encode convert
```php
// Out charset :UTF-8
// In charset :GB2312
phpCrawler::get('https://top.etao.com')->encoding('UTF-8','GB2312')->find('a')->texts();

// Out charset:UTF-8
// In charset:Automatic Identification
phpCrawler::get('https://top.etao.com')->encoding('UTF-8')->find('a')->texts();
```

#### HTTP Client (GuzzleHttp)
- Carry cookie login GitHub
```php
//Crawl GitHub content
$ql = phpCrawler::get('https://github.com','param1=testvalue & params2=somevalue',[
  'headers' => [
      // Fill in the cookie from the browser
      'Cookie' => 'SINAGLOBAL=546064; wb_cmtLike_2112031=1; wvr=6;....'
  ]
]);
//echo $ql->getHtml();
$userName = $ql->find('.header-nav-current-user>.css-truncate-target')->text();
echo $userName;
```
- Use the Http proxy
```php
$urlParams = ['param1' => 'testvalue','params2' => 'somevalue'];
$opts = [
	// Set the http proxy
    'proxy' => 'http://222.141.11.17:8118',
    //Set the timeout time in seconds
    'timeout' => 30,
     // Fake HTTP headers
    'headers' => [
        'Referer' => 'https://phpCrawler.cc/',
        'User-Agent' => 'testing/1.0',
        'Accept'     => 'application/json',
        'X-Foo'      => ['Bar', 'Baz'],
        'Cookie'    => 'abc=111;xxx=222'
    ]
];
$ql->get('http://httpbin.org/get',$urlParams,$opts);
// echo $ql->getHtml();
```

- Analog login
```php
// Post login
$ql = phpCrawler::post('http://xxxx.com/login',[
    'username' => 'admin',
    'password' => '123456'
])->get('http://xxx.com/admin');
// Crawl pages that need to be logged in to access
$ql->get('http://xxx.com/admin/page');
//echo $ql->getHtml();
```

#### Submit forms
Login GitHub
```php
// Get the phpCrawler instance
$ql = phpCrawler::getInstance();
// Get the login form
$form = $ql->get('https://github.com/login')->find('form');

// Fill in the GitHub username and password
$form->find('input[name=login]')->val('your github username or email');
$form->find('input[name=password]')->val('your github password');

// Serialize the form data
$fromData = $form->serializeArray();
$postData = [];
foreach ($fromData as $item) {
    $postData[$item['name']] = $item['value'];
}

// Submit the login form
$actionUrl = 'https://github.com'.$form->attr('action');
$ql->post($actionUrl,$postData);
// To determine whether the login is successful
// echo $ql->getHtml();
$userName = $ql->find('.header-nav-current-user>.css-truncate-target')->text();
if($userName)
{
    echo 'Login successful ! Welcome:'.$userName;
}else{
    echo 'Login failed !';
}
```
#### Bind function extension
Customize the extension of a `myHttp` method:
```php
$ql = phpCrawler::getInstance();

//Bind a `myHttp` method to the phpCrawler object
$ql->bind('myHttp',function ($url){
	// $this is the current phpCrawler object
    $html = file_get_contents($url);
    $this->setHtml($html);
    return $this;
});

// And then you can call by the name of the binding
$data = $ql->myHttp('https://toutiao.io')->find('h3 a')->texts();
print_r($data->all());
```
Or package to class, and then bind:
```php
$ql->bind('myHttp',function ($url){
    return new MyHttp($this,$url);
});
```

#### Plugin used
- Use the PhantomJS plugin to crawl JavaScript dynamically rendered pages:

```php
// Set the PhantomJS binary file path during installation
$ql = phpCrawler::use(PhantomJs::class,'/usr/local/bin/phantomjs');

// Crawl「500px」all picture links
$data = $ql->browser('https://500px.com/editors')->find('img')->attrs('src');
print_r($data->all());

// Use the HTTP proxy
$ql->browser('https://500px.com/editors',false,[
	'--proxy' => '192.168.1.42:8080',
    '--proxy-type' => 'http'
])
```

- Using the CURL multithreading plug-in, multi-threaded crawling GitHub trending :

```php
$ql = phpCrawler::use(CurlMulti::class);
$ql->curlMulti([
    'https://github.com/trending/php',
    'https://github.com/trending/go',
    //.....more urls
])
 // Called if task is success
 ->success(function (phpCrawler $ql,CurlMulti $curl,$r){
    echo "Current url:{$r['info']['url']} \r\n";
    $data = $ql->find('h3 a')->texts();
    print_r($data->all());
})
 // Task fail callback
->error(function ($errorInfo,CurlMulti $curl){
    echo "Current url:{$errorInfo['info']['url']} \r\n";
    print_r($errorInfo['error']);
})
->start([
	// Maximum number of threads
    'maxThread' => 10,
    // Number of error retries
    'maxTry' => 3,
]);

```

## Author
wenhainan  <whndeweilai@163.com>


## Lisence
phpCrawler is licensed under the license of MIT. See the LICENSE for more details.
