<?php

namespace WebScrap;

require_once '../vendor/autoload.php';

use Carbon\Carbon;
use Goutte\Client;

$client = new Client();

$crawler = $client->request('GET', 'http://www.jaffnamuslim.com/');

$crawler->filter('.post-title a')->each(function ($node)
use ($client) {
    $title = "";
    $publishedDate = "";
    $images = [];
    $content = "";
    $id = 0;

    $pageUrl = $node->link()->getUri();
    $page = $client->click($node->link());

    $header = $page->filter('h3 > a');
    $title = $header->text();

    $metaDate = $page->filter('.postmeta-primary-container > .postmeta-primary > .meta_date');
    $publishedDate = $metaDate->text();
    $pubDate = Carbon::parse($publishedDate);
    $publishedDate = $pubDate->format('Y-m-d');

    $page->filter('.post-body > div> div > a > img')->each(function ($image)
    use (&$images) {
        $images[] = $image->attr('src');
    });

    $page->filter('.post-body > div > div')->each(function ($body)
    use (&$content) {
        $content .=  '<p>' . $body->text() . '</p>';
    });

    echo $title . "<br />";
    echo $pageUrl . "<br />";
    echo  $publishedDate . "<br />";
    echo  $id . "<br />";
    print_r($images);
    echo $content;
    echo "<hr />";
});
