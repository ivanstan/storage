<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class FileStorageControllerTest extends WebTestCase
{
    use ApiTestProvider;

    public function testFileUpload(): void
    {
        $response = $this->request('POST', '/api/file/upload', [], [
            'file' => [
                $this->getFile(self::FILE1),
                $this->getFile(self::FILE2)
            ]
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
    public function testUploadExistingFile(): array
    {
        $response = $this->request('POST', '/api/file/upload', [], [
            'file' => [$this->getFile(self::FILE1)],
        ]);

        self::assertFileExists(__DIR__ . '/../public/data/' . $response[0]['id'] . '.txt');
        self::assertEquals('file1.txt', $response[0]['name']);
        self::assertEquals(18, $response[0]['size']);
        self::assertEquals('3f482a35ebe566c18436aedacac93da358a2ff31829851db485bf84c775f761f', $response[0]['sha256']);

        return $response;
    }

    /**
     * @depends testUploadExistingFile
     */
    public function testDelete(array $response): void
    {
        $this->request('DELETE', '/api/file/' . $response[0]['id'] . '/delete');

        self::assertEquals(Response::HTTP_ACCEPTED, $this->client->getResponse()->getStatusCode());
        self::assertFileDoesNotExist(__DIR__ . '/../public/data/' . $response[0]['id'] . '.txt');
    }
}
