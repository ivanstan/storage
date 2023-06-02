<?php

namespace App\Request;

class NodeListRequest extends Request
{
    public function getFilters(): array
    {
        return [
          'files' => $this->get('files')
        ];
    }
}
