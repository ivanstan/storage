<?php

namespace App\Command;

use GuzzleHttp\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:file-to-text',
    description: 'Add a short description for your command',
)]
class FileToTextCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $response = json_decode(file_get_contents('https://system:system@storage.ivanstanojevic.me/api/files?nodes=null&page=1'), true);

        $items = count($response['member']);

        while ($items > 0) {
            foreach ($response['member'] as $file) {

                $this->createNode(
                    $this->upload(
                        $this->download($file['destination'])
                    )
                );
            }

            $response = json_decode(file_get_contents('https://system:system@storage.ivanstanojevic.me/api/files?nodes=null&page=1'), true);
            $items = count($response['member']);
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }

    protected function download(string $url): string
    {
        $client = new Client();
        $destination = __DIR__ . '/item.tmp';

        $client->request('GET', $url, ['sink' => $destination, 'auth' => [
            'system',
            'system'
        ]]);

        return $destination;
    }

    protected function upload(string $download)
    {
        $client = new Client();

        $res = $client->request('POST', 'https://test.ivanstanojevic.me/api/recognize', [
            'auth' => ['system', 'system'],
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => file_get_contents($download),
                    'filename' => 'a'
                ],
            ],
        ]);

        return $res;
    }

    protected function createNode($response)
    {
        $payload = json_decode($response->getBody()->getContents());

        $client = new Client();
        $response = $client->post("https://system:system@storage.ivanstanojevic.me/api/node", [
            'json' => $payload
        ]);

        dd($response);

    }

}
