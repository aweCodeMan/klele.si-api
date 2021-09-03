<?php


namespace App\Services;


final class LinkData
{
    public function __construct(public ?string $title, public array $meta, public array $openGraph, public array $twitterCard)
    {
    }
}
