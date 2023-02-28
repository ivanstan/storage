<?php

namespace App\Controller;

use App\Entity\File;
use App\Repository\FileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ivanstan\SymfonySupport\Services\QueryBuilderPaginator;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Uid\Uuid;
use OpenApi\Attributes as OA;

#[Route('/api/file')]
class FileStorageController extends AbstractController
{
    public function __construct(
        protected string                 $publicDir,
        protected EntityManagerInterface $entityManager,
        protected NormalizerInterface    $normalizer,
        protected FileRepository         $repository,
    )
    {
    }

    #[Route('s', name: 'file_storage_index', methods: 'GET')]
    #[OA\Get(tags: ['File'])]
    public function index(): JsonResponse
    {
        $pagination = new QueryBuilderPaginator($this->repository->search());

        return $this->json($this->normalizer->normalize($pagination));
    }

    #[Route('/upload', name: 'file_storage_upload', methods: 'POST')]
    #[OA\Post(tags: ['File'])]
    #[OA\Parameter(
        name: 'file',
        required: true,
    )]
    public function upload(Request $request): JsonResponse
    {
        $entities = [];

        /** @var UploadedFile $file */
        foreach ($request->files->get('file') as $file) {
            $source = $file->getFileInfo()->getPathname();

            if ($existing = $this->repository->findOneBySha256(hash_file('sha256', $source))) {
                $entities[] = $existing;
                continue;
            }

            $id = Uuid::v4();
            $destination = $this->publicDir . '/data/' . $id . '.' . $file->getFileInfo()->getExtension();
            (new Filesystem())->copy($source, $destination, true);
            $entity = File::fromUploadedFile(new UploadedFile($destination, $file->getClientOriginalName()));

            $this->repository->save($entity);
            $entities[] = $entity;
        }

        $this->entityManager->flush();

        return new JsonResponse($this->normalizer->normalize($entities));
    }

    #[Route('/{file}/delete', name: 'file_storage_delete', methods: 'DELETE')]
    #[OA\Delete(tags: ['File'])]
    public function delete(File $file): JsonResponse
    {
        $this->repository->remove($file, true);
        (new Filesystem())->remove($file->getDestination());

        return $this->json([], Response::HTTP_ACCEPTED);
    }

    #[Route('/{file}/download', name: 'file_storage_download', methods: 'GET')]
    #[OA\Get(tags: ['File'])]
    public function get(File $file): BinaryFileResponse
    {
        return $this->file($file->getDestination(), $file->getName());
    }
}
