<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/', methods: 'GET')]
    public function index(): Response
    {
        return $this->render('api.html.twig');
    }

    #[Route('/api/doc', methods: 'GET')]
    public function api(Request $request): JsonResponse
    {
        $response = [
            "swagger" => "2.0",
            "info" => [
                "description" => "",
                "version" => "1.0",
                "title" => "Storage",
                "termsOfService" => "",
                "contact" => [
                    "email" => ""
                ],
                "license" => [
                    "name" => "Apache 2.0",
                    "url" => "http://www.apache.org/licenses/LICENSE-2.0.html"
                ]
            ],
            "host" => $request->getHost(),
            "tags" => [
                [
                    "name" => "",
                    "description" => "",
                    "externalDocs" => [
                        "description" => "",
                        "url" => ""
                    ]
                ],
            ],
            "schemes" => [
                "https",
                "http"
            ],
            "basePath" => "/",
            "paths" => [
                "/storage/upload" => [
                    "post" => [
//                        "tags" => [
//                            "file"
//                        ],
                        "summary" => "uploads an file",
                        "description" => "",
                        "operationId" => "uploadFile",
                        "consumes" => [
                            "multipart/form-data"
                        ],
                        "produces" => [
                            "application/json"
                        ],
                        "parameters" => [
                            [
                                "name" => "file[]",
                                "in" => "query",
                                "description" => "ID of pet to update",
                                "required" => true,
                                "type" => "array",
                                "items" => [
                                    "type" => "file"
                                ],
                            ],
//                        "responses" => [
//                            [
//                                "description" => "successful operation",
//                                "schema" => [
//                                    '$ref' => "#/definitions/ApiResponse"
//                                ]
//                            ]
//                        ],
//                        "security" => [
//                            [
//                                "petstore_auth" => [
//                                    "write:pets",
//                                    "read:pets"
//                                ]
//                            ]
                        ]
                    ]
                ],
            ]
        ];

        return $this->json($response);
    }
}
