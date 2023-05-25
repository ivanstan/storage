<?php

namespace App\Tests\Provider;

use App\Entity\File;
use Doctrine\ORM\EntityManagerInterface;

class TestDataProvider
{
    public function __construct(protected EntityManagerInterface $em)
    {
    }

    public function createFile(): File
    {
        $file = new File();
        $file->setName('Test');
        $file->setDestination(__DIR__ . '/data/file1.txt');
        $file->setSize(1);
        $file->setSha256('dummy');
        $file->setMime('text/plain');
        $file->setCreatedAt(new \DateTimeImmutable());
        $file->setUpdatedAt(new \DateTimeImmutable());

        $this->em->persist($file);
        $this->em->flush();

        return $file;
    }
}
