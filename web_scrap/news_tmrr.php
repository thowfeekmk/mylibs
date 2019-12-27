<?php

namespace WebScrap;

require_once '../vendor/autoload.php';

use Carbon\Carbon;
use Goutte\Client;

$client = new Client();

$crawler = $client->request('GET', 'http://www.tamilmirror.lk/news/175');

$crawler->filter('.card >  div > div > div > a')->each(function ($node)
use ($client) {
    $title = "";
    $publishedDate = "";
    $images = [];
    $content = "";
    $id = 0;

    if ($node->attr('href') != '#') {
        $pageUrl = $node->link()->getUri();
        $page = $client->click($node->link());

        $header = $page->filter('header > h1');
        $title = $header->text();

        $meta = $page->filter('meta');
        $meta->each(function ($md)
        use (&$publishedDate, &$images) {
            if ($md->attr('itemprop') == "datePublished") {
                $publishedDate = $md->attr('content');
            }
            if ($md->attr('property') == "og:image") {
                $images[] = $md->attr('content');
            }
        });
        $pubDate = Carbon::parse($publishedDate);
        $publishedDate = $pubDate->format('Y-m-d H:i:s');

        $page->filter('.inner-content > p')->each(function ($body)
        use (&$content) {
            $content .=  '<p>' . $body->text() . '</p>';
        });

        $idArr = explode('/', $pageUrl);
        $idArr2 = explode('-', $idArr[5]);
        $id = $idArr2[1];

        echo $title . "<br />";
        echo $pageUrl . "<br />";
        echo  $publishedDate . "<br />";
        echo  $id . "<br />";
        print_r($images);
        echo $content;
        echo "<hr />";
    }
});
