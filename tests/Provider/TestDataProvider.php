<?php

namespace App\Tests\Provider;

use App\Entity\File;
use App\Entity\Node;

trait TestDataProvider
{
    public function createFile(): File
    {
        $this->getClient();
        $em = self::getContainer()->get('doctrine.orm.entity_manager');

        $file = new File();
        $file->setName('Test');
        $file->setDestination(__DIR__ . '/data/file1.txt');
        $file->setSize(1);
        $file->setSha256('dummy');
        $file->setMime('text/plain');
        $file->setCreatedAt(new \DateTimeImmutable());
        $file->setUpdatedAt(new \DateTimeImmutable());

        $em->persist($file);
        $em->flush();

        return $file;
    }

    public function createNode(File $file = null): Node
    {
        $this->getClient();
        $em = self::getContainer()->get('doctrine.orm.entity_manager');

        $node = new Node();
        $node->setData(['test' => 123]);

        if ($file) {
            $node->addFile($file);
        }

        $em->persist($node);
        $em->flush();

        return $node;
    }

    public function deleteEntity($entity): void
    {
        $em = self::getContainer()->get('doctrine.orm.entity_manager');
        $em->remove($entity);
        $em->flush();
    }
}
