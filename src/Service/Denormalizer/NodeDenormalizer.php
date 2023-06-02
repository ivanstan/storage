<?php

namespace App\Service\Denormalizer;

use App\Entity\File;
use App\Entity\Node;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class NodeDenormalizer implements DenormalizerInterface
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function denormalize($data, $type, $format = null, array $context = [])
    {
        /** @var Node $node */
        if ($context['object_to_populate'] ?? null instanceof Node) {
            $node = $context['object_to_populate'];
        } else {
            $node = new Node();
        }

        $node->setData($data['data']);

        foreach ($data['files'] ?? [] as $fileId) {
            $fileEntity = $this->entityManager->getRepository(File::class)->find($fileId);

            if (!$fileEntity) {
                throw new BadRequestHttpException(\sprintf('File with identifier %s is not found', $fileId));
            }

            $node->addFile($fileEntity);
        }

        return $node;
    }

    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return $type === Node::class;
    }
}
