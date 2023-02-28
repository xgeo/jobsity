<?php

namespace App\Api\Controllers;

use App\Api\Services\UserService;
use Laminas\Diactoros\Response\JsonResponse;
use OpenApi\Annotations as OA;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @OA\Tag(name="Users")
 */
class UserController extends BaseController
{
    /**
     * @param ContainerInterface $container
     * @param UserService $userService
     */
    public function __construct(ContainerInterface $container,
                                private readonly UserService $userService)
    {
        parent::__construct($container);
    }

    /**
     * @OA\Post(
     *     tags={"Users"},
     *     path="/user/auth",
     *     description="User Authentication",
     *     operationId="auth",
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *      mediaType="application/json",
     *      @OA\Schema(
     *          @OA\Property(property="email", type="string", format="email"),
     *          @OA\Property(property="password", type="string"),
     *      )
     *     )
     *     ),
     *     @OA\Response(response="200",
     *     description="An example resource")
     * )
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function auth(ServerRequestInterface $request): ResponseInterface
    {
        $data = json_decode($request->getBody()->getContents());

        $credResponse = $this->userService->getCredentials(
            [
                'email'     => $data->email,
                'password'  => $data->password
            ]
        );

        if (!$credResponse) {
            return new JsonResponse(['message' => 'AUTH_FAIL'], 401);
        }

        return new JsonResponse($credResponse, 200);
    }

    /**
     * @OA\Post(
     *     tags={"Users"},
     *     path="/user",
     *     description="Create a new user",
     *     operationId="store",
     *     security={ {"bearerAuth": {} }},
     *     @OA\RequestBody(
     *      @OA\MediaType(
     *      mediaType="application/json",
     *      @OA\Schema(
     *          @OA\Property(property="email", type="string", format="email"),
     *          @OA\Property(property="password", type="string"),
     *      )
     *     )
     *     ),
     *     @OA\Response(response="201",
     *     description="An example resource")
     * )
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function store(ServerRequestInterface $request,
                          ResponseInterface $response): ResponseInterface
    {
        $data = json_decode($request->getBody()->getContents());

        $user = $this->userService->getUserModel($data);

        $userCreated = $this->userService->store($user);

        if (!$userCreated) {
            return new JsonResponse(['message' => 'USER_CREATE_FAIL'], 500);
        }

        return new JsonResponse([
            'id' => $userCreated->getId(),
            'email' => $userCreated->getEmail()
        ], 201);
    }
}