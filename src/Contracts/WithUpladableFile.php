<?php


namespace App\Contracts;


use Symfony\Component\HttpFoundation\Request;

interface WithUpladableFile
{

    public function getEntityTypeIdentifier(): string;

}
