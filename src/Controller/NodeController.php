<?php

namespace App\Controller;

use App\Entity\Node;
use App\Repository\NodeRepository;
use App\Request\NodeListRequest;
use App\Request\Request;
use Doctrine\ORM\EntityManagerInterface;
use Ivanstan\SymfonySupport\Services\QueryBuilderPaginator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/node')]
class NodeController extends AbstractApiController
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected SerializerInterface    $serializer,
        protected DenormalizerInterface $denormalizer,
        protected ValidatorInterface     $validator,
        protected NodeRepository         $repository,
    )
    {
    }

    #[Route('s', name: 'api_node_list', methods: ['GET'],)]
    public function list(NodeListRequest $request): JsonResponse
    {
        $pagination = new QueryBuilderPaginator($this->repository->search($request->getFilters()));

        return $this->json($pagination);
    }

    #[Route('/{id}', name: 'api_node_show', methods: ['GET'],)]
    public function read(Node $node): JsonResponse
    {
        return $this->json($node);
    }

    #[Route('', name: 'api_node_create', methods: ['POST'],)]
    public function create(Request $request): JsonResponse
    {
        $node = $this->denormalizer->denormalize($request->getArrayContent(), Node::class);

        $errors = $this->validator->validate($node);

        if (count($errors) === 0) {
            $this->repository->save($node, true);

            return $this->json($node, Response::HTTP_CREATED);
        }

        $errors = $this->getErrorsAsArray($errors);

        return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'api_node_update', methods: ['PUT', 'PATCH'],)]
    public function update(Request $request, Node $node): JsonResponse
    {
        $node = $this->denormalizer->denormalize($request->getArrayContent(), Node::class, context: [
            'object_to_populate' => $node,
        ]);

        $errors = $this->validator->validate($node);

        if (count($errors) === 0) {
            $this->repository->save($node, true);

            return $this->json($node);
        }

        return $this->json(['errors' => $this->getErrorsAsArray($errors)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'api_node_delete', methods: ['DELETE'],)]
    public function delete(Node $node): JsonResponse
    {
        $this->repository->remove($node, true);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
