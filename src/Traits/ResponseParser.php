<?php

namespace Cyberfusion\Oxxa\Traits;

use Symfony\Component\DomCrawler\Crawler;

trait ResponseParser
{
    protected function getStatusCode(Crawler $crawler): string
    {
        return $crawler
            ->filter('channel > order > status_code')
            ->text();
    }

    protected function getStatusDescription(Crawler $crawler): string
    {
        return $crawler
            ->filter('channel > order > status_description')
            ->text();
    }
}
