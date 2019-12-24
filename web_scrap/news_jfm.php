<?php

namespace WebScrap;

require_once '../vendor/autoload.php';

use Carbon\Carbon;
use Goutte\Client;

$client = new Client();

$crawler = $client->request('GET', 'http://www.jaffnamuslim.com/');

// $link = $crawler->selectLink('.item-content')->link();
// $crawler = $client->click($link);

// $crawler->filter('.post__excerpt > p')->each(function ($node) {
//     print $node->text() . "\n";
// });

$crawler->filter('.post-title a')->each(function ($node)
use ($client) {
    //print $node->text() . "<br />";
    //print $node->link()->getUri() . "<br />";
    $pageUrl = $node->link()->getUri();
    $page = $client->click($node->link());

    $header = $page->filter('h3 > a');
    $metaDate = $page->filter('.postmeta-primary-container > .postmeta-primary > .meta_date');
    $title = $header->text();
    $publishedDate = $metaDate->text();
    $pubDate = Carbon::parse($publishedDate);
    $images = [];

    $page->filter('.post-body > div> div > a > img')->each(function ($image)
    use (&$images) {
        $images[] = $image->attr('src');
    });

    echo $title . "<br />";
    echo $pageUrl . "<br />";
    echo $pubDate->format('Y-m-d') . "<br />";
    print_r($images);

    $content = "";
    $page->filter('.post-body > div > div')->each(function ($body)
    use (&$content) {
        $content .=  '<p>' . $body->text() . '</p>';
    });

    echo $content;
    echo "<hr />";
});
