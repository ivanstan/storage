<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AbstractApiController extends AbstractController
{
    protected function getErrorsAsArray($errors): array
    {
        $result = [];
        foreach ($errors as $error) {
            $result[] = $error->getMessage();
        }

        return $result;
    }
}
