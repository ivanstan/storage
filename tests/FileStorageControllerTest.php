<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileStorageControllerTest extends WebTestCase
{
    use ApiTestProvider;

    public function testFileUpload(): void
    {
        $response =  $this->request('POST', '/storage/upload', [], [
            'file[1]' => $this->getFile(self::FILE1),
            'file[2]' => $this->getFile(self::FILE2),
        ]);

        self::assertResponseIsSuccessful();

        self::assertFileExists(__DIR__ . '/../public/data/' . $response[0]['id'] . '.txt');
        self::assertEquals('file1.txt', $response[0]['name']);
        self::assertEquals(18, $response[0]['size']);
        self::assertEquals('3f482a35ebe566c18436aedacac93da358a2ff31829851db485bf84c775f761f', $response[0]['sha256']);

        self::assertFileExists(__DIR__ . '/../public/data/' . $response[1]['id'] . '.txt');
        self::assertEquals('file2.txt', $response[1]['name']);
        self::assertEquals(18, $response[1]['size']);
        self::assertEquals('3f482a35ebe566c18436aedacac93da358a2ff31829851db485bf84c775f761f', $response[0]['sha256']);
    }

    /**
     * @depends testFileUpload
     */
    public function testUploadExistingFile(): void
    {
        $response = $this->request('POST', '/storage/upload', [], [
            'file[1]' => $this->getFile(self::FILE1),
        ]);

        self::assertFileExists(__DIR__ . '/../public/data/' . $response[0]['id'] . '.txt');
        self::assertEquals('file1.txt', $response[0]['name']);
        self::assertEquals(18, $response[0]['size']);
        self::assertEquals('3f482a35ebe566c18436aedacac93da358a2ff31829851db485bf84c775f761f', $response[0]['sha256']);
    }
}
