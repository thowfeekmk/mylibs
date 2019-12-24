<?php

namespace WebScrap;

require_once '../vendor/autoload.php';


use Goutte\Client;


$client = new Client();


$crawler = $client->request('GET', 'http://www.symfony.com/blog/');

//$client->getClient()->setDefaultOption('config/curl/' . CURLOPT_TIMEOUT, 60);

// Click on the "Security Advisories" link
$link = $crawler->selectLink('Security Advisories')->link();
$crawler = $client->click($link);

// Get the latest post in this category and display the titles
// $crawler->filter('h2 > a')->each(function ($node) {
//     print $node->text() . "\n";
// });

// $crawler->filter('.post__excerpt > p')->each(function ($node) {
//     print $node->text() . "\n";
// });
