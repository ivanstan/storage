<?php

namespace App\Controller;

use App\Entity\Node;
use App\Repository\NodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ivanstan\SymfonySupport\Services\QueryBuilderPaginator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/node')]
class NodeController extends AbstractApiController
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected SerializerInterface    $serializer,
        protected ValidatorInterface     $validator,
        protected NodeRepository         $repository,
    )
    {
    }

    #[Route('s', name: 'api_node_list', methods: ['GET'],)]
    public function index(): JsonResponse
    {
        $pagination = new QueryBuilderPaginator($this->repository->search());

        return $this->json($pagination);
    }

    #[Route('/{id}', name: 'api_node_show', methods: ['GET'],)]
    public function show(Node $node): JsonResponse
    {
        return $this->json($node);
    }

    #[Route('', name: 'api_node_create', methods: ['POST'],)]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $node = Node::fromArray($data);

        $errors = $this->validator->validate($node);

        if (count($errors) === 0) {
            $this->entityManager->persist($node);
            $this->entityManager->flush();

            return $this->json($node, Response::HTTP_CREATED);
        }

        $errors = $this->getErrorsAsArray($errors);

        return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'api_node_update', methods: ['PUT', 'PATCH'],)]
    public function update(Request $request, Node $node): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $node = $node->fill($data);

        $errors = $this->validator->validate($node);

        if (count($errors) === 0) {
            $this->entityManager->flush();

            return $this->json($node);
        }

        return $this->json(['errors' => $this->getErrorsAsArray($errors)], Response::HTTP_BAD_REQUEST);
    }

    #[Route('/{id}', name: 'api_node_delete', methods: ['DELETE'],)]
    public function delete(Node $node): JsonResponse
    {
        $this->repository->remove($node);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
