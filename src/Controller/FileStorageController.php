<?php

namespace App\Controller;

use App\Entity\File;
use App\Repository\FileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\FileBag;
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
    public function index(Request $request, FileRepository $repository): JsonResponse
    {
        $entities = [];

        /** @var UploadedFile $file */
        foreach ($request->files->get('file') as $file) {
            $source = $file->getFileInfo()->getPathname();

            if ($existing = $repository->findOneBySha256(hash_file('sha256', $source))) {
                $entities[] = $existing;
                continue;
            }

            $id = Uuid::v4();
            $destination = $this->publicDir . '/data/' . $id . '.' . $file->getFileInfo()->getExtension();
            (new Filesystem())->copy($source, $destination, true);
            $entity = File::fromUploadedFile(new UploadedFile($destination, $file->getClientOriginalName()));

            $repository->save($entity);
            $entities[] = $entity;
        }

        $this->entityManager->flush();

        return new JsonResponse($this->normalizer->normalize($entities));
    }
}
