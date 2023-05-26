<?php

namespace App\Tests\Controller;

use App\Tests\Provider\ApiTestProvider;
use App\Tests\Provider\TestDataProvider;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Uid\Uuid;

class NodeControllerTest extends WebTestCase
{
    use ApiTestProvider;
    use TestDataProvider;

    public function testList(): void
    {
        $this->request(Request::METHOD_GET, '/api/nodes');

        static::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertJson($this->client->getResponse()->getContent());
    }

    public function testInvalidJsonPayload(): void
    {
        $this->request(Request::METHOD_POST, '/api/node', content: '{');
        static::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @depends testList
     */
    public function testCreate(): array
    {
        $data = [
            'data' => [
                'test' => 123,
            ],
        ];

        $response = $this->request(Request::METHOD_POST, '/api/node', content: json_encode($data));

        static::assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        static::assertJson($this->client->getResponse()->getContent());
        static::assertArrayHasKey('id', $response);
        static::assertArrayHasKey('files', $response);
        static::assertEquals($data['data'], $response['data']);
        static::assertEquals('Node', $response['@type']);

        return $response;
    }

    /**
     * @depends testCreate
     */
    public function testUpdate(array $data): array
    {
        $file = $this->createFile();

        $payload = [
            'data' => [
                'test' => 456,
            ],
            'files' => [
                'id' => $file->getId()->jsonSerialize(),
            ]
        ];

        $response = $this->request(Request::METHOD_PUT, '/api/node/' . $data['id'], content: json_encode($payload));

        static::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertJson($this->client->getResponse()->getContent());
        static::assertEquals($response['data'], $payload['data']);

        return $response;
    }

    public function testCreateNodeWithNonExistingFile(): void
    {
        $node = $this->createNode();

        $payload = [
            'data' => [
                'test' => 456,
            ],
            'files' => [
                'id' => Uuid::v4(),
            ]
        ];

        $this->request(Request::METHOD_PUT, '/api/node/' . $node->getId(), content: json_encode($payload));
        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());

        $this->deleteEntity($node);
    }

    public function testRead(): void
    {
        $node = $this->createNode(
            $this->createFile()
        );

        $response = $this->request(Request::METHOD_GET, '/api/node/' . $node->getId()->jsonSerialize());
        static::assertEquals(['test' => 123], $response['data']);
        static::assertEquals('Test', $response['files'][0]['name']);

        $this->deleteEntity($node);
    }

    /**
     * @depends testUpdate
     */
    public function testDelete(array $data): void
    {
        $this->request(Request::METHOD_DELETE, '/api/node/' . $data['id']);

        static::assertEquals(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }

    public function testNodesWithoutFiles(): void
    {
        $this->createNode();
        $this->createNode(
          $this->createFile()
        );

        $response = $this->request(Request::METHOD_GET, '/api/nodes?files=null');

        static::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        static::assertJson($this->client->getResponse()->getContent());
        static::assertCount(1, $response['member']);
    }
}
