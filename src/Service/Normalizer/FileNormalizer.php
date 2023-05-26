<?php

namespace App\Service\Normalizer;

use App\Entity\File;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FileNormalizer implements NormalizerInterface
{
    public function __construct(protected UrlGeneratorInterface $generator)
    {
    }

    /**
     * @param File $object
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        return [
            '@id' => $object->getId(),
            '@type' => 'File',
            'id' => $object->getId(),
            'name' => $object->getName(),
            'mime' => $object->getMime(),
            'size' => $object->getSize(),
            'destination' => $this->generator->generate(
                'file_storage_download',
                ['file' => $object->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'createdAt' => $object->getCreatedAt(),
            'updatedAt' => $object->getUpdatedAt(),
            'sha256' => $object->getSha256(),
        ];
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof File;
    }
}
