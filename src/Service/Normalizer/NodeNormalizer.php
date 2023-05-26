<?php

namespace App\Service\Normalizer;

use App\Entity\Node;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class NodeNormalizer implements NormalizerInterface
{
    public function __construct(protected FileNormalizer $fileNormalizer)
    {
    }

    /**
     * @param Node $object
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        $files = [];
        foreach ($object->getFiles() as $file) {
            $files[] = $this->fileNormalizer->normalize($file);
        }

        return [
            'id' => $object->getId(),
            '@type' => 'Node',
            'data' => $object->getData(),
            'files' => $files,
        ];
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof Node;
    }
}
