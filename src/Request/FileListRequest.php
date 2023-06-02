<?php

namespace App\Request;

class FileListRequest extends \Symfony\Component\HttpFoundation\Request
{
    public function getFilters(): array
    {
        return [
            'nodes' => $this->get('nodes')
        ];
    }
}
