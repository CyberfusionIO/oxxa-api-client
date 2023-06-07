<?php

namespace Cyberfusion\Oxxa\Contracts;

use Symfony\Component\DomCrawler\Crawler;

interface OxxaClient
{
    public function request(array $parameters = []): Crawler;
}
