<?php

namespace App\Controller;

use App\Entity\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Uid\Uuid;

#[Route('/storage')]
class FileStorageController extends AbstractController
{
    public function __construct(
        protected string $publicDir,
        protected EntityManagerInterface $entityManager,
        protected NormalizerInterface $normalizer
    ) {
    }

    #[Route('/upload', name: 'file_storage_upload', methods: 'POST')]
    public function index(Request $request): JsonResponse
    {
        /** @var UploadedFile $file */
        foreach ($request->files as $file) {
            $id = Uuid::v4();

            $source = $file->getFileInfo()->getPathname();
            $destination = $this->publicDir . '/data/' . $id . '.' . $file->getFileInfo()->getExtension();
            (new Filesystem())->copy($source, $destination, true);

            $entity = File::fromUploadedFile(new UploadedFile($destination, $file->getClientOriginalName()));

            $this->entityManager->persist($entity);
        }

        $this->entityManager->flush();

        return new JsonResponse($this->normalizer->normalize($entity));
    }
}
