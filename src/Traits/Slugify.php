<?php


namespace App\Traits;


trait Slugify
{

    public function slugFor(string $text): string
    {
        $slugify = new \Cocur\Slugify\Slugify();
        return $slugify->slugify($text);
    }

}
