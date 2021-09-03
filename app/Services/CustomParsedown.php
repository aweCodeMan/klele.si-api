<?php


namespace App\Services;


class CustomParsedown extends \Parsedown
{
    protected function inlineLink($Excerpt)
    {
        $result = parent::inlineLink($Excerpt);

        $result['element']['attributes']['ref'] = 'noopener noreferrer';
        $result['element']['attributes']['target'] = '_blank';

        return $result;
    }
}
