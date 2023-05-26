<?php

namespace App\Request;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class Request extends \Symfony\Component\HttpFoundation\Request
{
    public function getArrayContent(): array
    {
        try {
            return json_decode($this->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new BadRequestException($e->getMessage());
        }
    }
}
