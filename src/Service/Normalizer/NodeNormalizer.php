<?php

namespace App\Service\Normalizer;

use App\Entity\Node;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class NodeNormalizer implements NormalizerInterface
{
    /**
     * @param Node $object
     */
    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        return [
            'id' => $object->getId(),
            '@type' => 'Node',
            'data' => $object->getData(),
            'files' => $object->getFiles(),
        ];
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {

    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null)
    {
        return $type === Node::class;
    }

    public function supportsNormalization(mixed $data, string $format = null)
    {
        return $data instanceof Node;
    }
}
