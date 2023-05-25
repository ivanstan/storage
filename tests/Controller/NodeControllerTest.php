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
    public function testCreateNode(): void
    {
        $data = [
            'data' => [
                'test' => 123,
            ],
        ];

        $this->client->request('POST', '/api/nodes', [], [], [], json_encode($data));

        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());

        // Additional assertions for created node data
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals($data['title'], $responseData['title']);
        $this->assertEquals($data['body'], $responseData['body']);
    }

    /**
     * @depends testCreateNode
     */
    public function testUpdateNode(): void
    {
        $data = [
            'data' => [
                'test' => 456,
            ],
        ];

        $createdNode = $this->getNodeByTitle('Test Node');
        $this->client->request('PUT', '/api/nodes/' . $createdNode['id'], [], [], [], json_encode($data));

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());

        // Additional assertions for updated node data
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($data['title'], $responseData['title']);
        $this->assertEquals($data['body'], $responseData['body']);
    }

    /**
     * @depends testCreateNode
     */
    public function testDeleteNode(): void
    {
        $createdNode = $this->getNodeByTitle('Test Node');
        $this->client->request('DELETE', '/api/nodes/' . $createdNode['id']);

        $this->assertEquals(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }

    private function getNodeByTitle(string $title): ?array
    {
        $response = $this->client->request('GET', '/api/nodes');
        $nodes = json_decode($response->getContent(), true);

        foreach ($nodes as $node) {
            if ($node['title'] === $title) {
                return $node;
            }
        }

        return null;
    }

    public function testCreateNodeValidationFailure(): void
    {
        $data = [
            // Missing 'title' field
            'body' => 'Lorem ipsum dolor sit amet',
        ];

        $this->client->request('POST', '/api/nodes', [], [], [], json_encode($data));

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('errors', $responseData);
        $this->assertCount(1, $responseData['errors']);
        $this->assertArrayHasKey('title', $responseData['errors']);
    }
}
