<?php

namespace App\Services;

final class MarkdownService
{

    public static function parse(string $markdown): string
    {
        $html = (new CustomParsedown())->setSafeMode(true)->text($markdown);
        $purifier = new \HTMLPurifier(['HTML.TargetNoreferrer' => true, 'HTML.TargetNoopener' => true, 'Attr.AllowedFrameTargets' => ['_blank']]);

        return $purifier->purify($html);
    }
}
