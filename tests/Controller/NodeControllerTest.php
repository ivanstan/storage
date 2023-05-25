<?php

namespace App\Tests\Controller;

use App\Tests\Provider\ApiTestProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class NodeControllerTest extends WebTestCase
{
    use ApiTestProvider;

    public function testNodeList(): void
    {
        $this->request('GET', '/api/nodes');

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
    }

    /**
     * @depends testNodeList
     */
    public function testCreateNode(): array
    {
        $data = [
            'data' => [
                'test' => 123,
            ],
        ];

        $response = $this->request('POST', '/api/node', [], [], [], json_encode($data));

        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertArrayHasKey('id', $response);
        $this->arrayHasKey('files', $response);
        $this->assertEquals($data['data'], $response['data']);
        $this->assertEquals('Node', $response['@type']);

        return $response;
    }

    /**
     * @depends testCreateNode
     */
    public function testUpdateNode(array $data): array
    {
        $payload = [
            'data' => [
                'test' => 456,
            ],
        ];

        $response = $this->request('PUT', '/api/node/' . $data['id'], [], [], [], json_encode($payload));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertEquals($response['data'], $payload['data']);

        return $response;
    }

    /**
     * @depends testUpdateNode
     */
    public function testDeleteNode(array $data): void
    {
        $this->request('DELETE', '/api/node/' . $data['id']);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }
}
