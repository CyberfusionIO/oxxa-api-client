<?php

namespace Cyberfusion\Oxxa\Endpoints;

use Cyberfusion\Oxxa\Oxxa;
use Symfony\Component\DomCrawler\Crawler;

abstract class Endpoint
{
    public function __construct(protected Oxxa $client)
    {
    }

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
