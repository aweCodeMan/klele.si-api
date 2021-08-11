<?php

namespace App\Services;

final class MarkdownService
{

    public static function parse(string $markdown): string
    {
        $html = (new \Parsedown())->setSafeMode(true)->text($markdown);
        $purifier = new \HTMLPurifier(null);

        return $purifier->purify($html);
    }
}
