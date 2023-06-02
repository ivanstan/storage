<?php

namespace App\Tests\Provider;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait ApiTestProvider
{
    protected const FILE1 = 'file1.txt';
    protected const FILE2 = 'file2.txt';

    protected ?KernelBrowser $client = null;

    protected function getFile($file): UploadedFile
    {
        return new UploadedFile(
            __DIR__ . '/data/' . $file,
            $file
        );
    }

    protected function getClient()
    {
        if ($this->client) {
            return $this->client;
        }

        $server = [
            'PHP_AUTH_USER' => 'system',
            'PHP_AUTH_PW' => 'system',
        ];

        $this->client = static::createClient([], $server);

        return $this->client;
    }

    protected function request(string $method, string $uri, array $parameters = [], array $files = [], array $server = [], string $content = null, bool $changeHistory = true): ?array
    {
        if (!$this->client) {
            $this->getClient();
        }

        $this->client->request($method, $uri, $parameters, $files, $server, $content, $changeHistory);

        return json_decode($this->client->getResponse()->getContent(), true);
    }
}
