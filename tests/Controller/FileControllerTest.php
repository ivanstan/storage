<?php

namespace App\Tests\Controller;

use App\Tests\Provider\ApiTestProvider;
use App\Tests\Provider\TestDataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function PHPUnit\Framework\assertEquals;

class FileControllerTest extends WebTestCase
{
    use ApiTestProvider;
    use TestDataProvider;

    /**
     * @covers \App\Controller\FileController::upload
     */
    public function testUpload(): void
    {
        $response = $this->request(Request::METHOD_POST, '/api/file/upload', [], [
            'file' => [
                $this->getFile(self::FILE1),
                $this->getFile(self::FILE2)
            ]
        ]);

        self::assertResponseIsSuccessful();

        self::assertFileExists(__DIR__ . '/../../public/data/' . $response[0]['id'] . '.txt');
        self::assertEquals('file1.txt', $response[0]['name']);
        self::assertEquals(18, $response[0]['size']);
        self::assertEquals('3f482a35ebe566c18436aedacac93da358a2ff31829851db485bf84c775f761f', $response[0]['sha256']);

        self::assertFileExists(__DIR__ . '/../../public/data/' . $response[1]['id'] . '.txt');
        self::assertEquals('file2.txt', $response[1]['name']);
        self::assertEquals(18, $response[1]['size']);
        self::assertEquals('3f482a35ebe566c18436aedacac93da358a2ff31829851db485bf84c775f761f', $response[0]['sha256']);
    }

    /**
     * @covers \App\Controller\FileController::upload
     * @depends testUpload
     */
    public function testUploadExisting(): array
    {
        $response = $this->request(Request::METHOD_POST, '/api/file/upload', [], [
            'file' => [$this->getFile(self::FILE1)],
        ]);

        self::assertFileExists(__DIR__ . '/../../public/data/' . $response[0]['id'] . '.txt');
        self::assertEquals('file1.txt', $response[0]['name']);
        self::assertEquals(18, $response[0]['size']);
        self::assertEquals('3f482a35ebe566c18436aedacac93da358a2ff31829851db485bf84c775f761f', $response[0]['sha256']);

        return $response;
    }

    /**
     * @covers \App\Controller\FileController::list
     * @depends testUploadExisting
     */
    public function testSearch(): array
    {
        $response = $this->request(Request::METHOD_GET, '/api/files');

        self::assertEquals(2, $response['totalItems']);

        return $response;
    }

    /**
     * @covers \App\Controller\FileController::get
     * @depends testSearch
     */
    public function testDownload(array $response): array
    {
        $this->client = $this->getClient();
        $this->client->request(Request::METHOD_GET, $response['member'][0]['destination']);

        assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        return $response;
    }

    /**
     * @covers \App\Controller\FileController::delete
     * @depends testDownload
     */
    public function testDelete(array $response): void
    {
        foreach ($response['member'] as $item) {
            $this->request(Request::METHOD_DELETE, '/api/file/' . $item['id'] . '/delete');

            self::assertEquals(Response::HTTP_ACCEPTED, $this->client->getResponse()->getStatusCode());
            self::assertFileDoesNotExist(__DIR__ . '/../../public/data/' . $item['id'] . '.txt');
        }
    }

    /**
     * @covers \App\Controller\FileController::list
     */
    public function testFilesWithoutFiles(): void
    {
        $file = $this->createFile();
        $this->createNode(
            $this->createFile()
        );

        $response = $this->request(Request::METHOD_GET, '/api/files?nodes=null');

        static::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertJson($this->client->getResponse()->getContent());
        static::assertCount(1, $response['member']);

        $this->deleteEntity($file);
    }
}
