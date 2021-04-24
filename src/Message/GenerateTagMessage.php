<?php

namespace App\Message;


class GenerateTagMessage
{
    private $people;

    public function __construct(array $people)
    {
        $this->people = $people;
    }

    public function getPeople(): array
    {
        return $this->people;
    }
}