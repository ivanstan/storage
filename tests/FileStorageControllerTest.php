<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileStorageControllerTest extends WebTestCase
{
    public function testFileUpload(): void
    {
        $file = new UploadedFile(
            __DIR__ . '/data/file.txt',
            'file.txt'
        );

        $server = [
            'PHP_AUTH_USER' => 'system',
            'PHP_AUTH_PW' => 'system',
        ];

        $client = static::createClient([], $server);

        $client->request('POST', '/storage/upload', [], [
            'file[]' => $file,
        ]);

        self::assertResponseIsSuccessful();

        $response = json_decode($client->getResponse()->getContent(), true);

        self::assertFileExists(__DIR__ . '/../public/data/' . $response['id'] . '.txt');
        self::assertEquals('file.txt', $response['file']);
        self::assertEquals(0, $response['size']);
        self::assertEquals('e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855', $response['sha256']);
    }
}
