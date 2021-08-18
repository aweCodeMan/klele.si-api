<?php

namespace App\Services;

use spekulatius\phpscraper;

final class LinkService
{
    public static function parse(string $link): LinkData
    {
        $parser = new phpscraper();
        $parser->go($link);
        return new LinkData($parser->title, $parser->metaTags, $parser->openGraph, $parser->twitterCard);
    }
}
