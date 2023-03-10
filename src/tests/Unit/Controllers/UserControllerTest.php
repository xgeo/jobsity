<?php

namespace Unit\Controllers;

use App\Api\Controllers\UserController;
use App\Api\Services\StockService;
use App\Api\Services\UserService;
use App\Models\User;
use DI\Container;
use Firebase\JWT\JWT;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserControllerTest extends TestCase
{
    private UserController $userController;

    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $container = new Container();
        $this->userService = $this->createMock(UserService::class);
        $container->set(UserService::class, $this->userService);

        $this->userController = new UserController($container, $container->get(UserService::class));
    }

    public function testAuthSuccess()
    {

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getBody')->willReturn(new class {
                public function getContents()
                {
                    return json_encode([
                        'email' => 'test@test.br',
                        'password' => '123123'
                    ]);
                }
            });

        $response = $this->createMock(ResponseInterface::class);

        $this->userService->method('getCredentials')
            ->willReturn(['token' => JWT::encode([
                'email' => 'test@test.br',
                'password' => '123123'
            ], 'jobsity')]);


        $responseInterface = $this->userController->auth($request, $response);

        $this->assertSame(200, $responseInterface->getStatusCode());
    }

    public function testAuthFail()
    {
        $request = $this->createStub(ServerRequestInterface::class);

        $request->method('getBody')->willReturn(new class {
            public function getContents()
            {
                return json_encode([
                    'email' => 'test@test.br',
                    'password' => 'xxxx'
                ]);
            }
        });

        $response = $this->createMock(ResponseInterface::class);

        $this->userService->method('getCredentials')
            ->willReturn(null);

        $responseInterface = $this->userController->auth($request, $response);

        $this->assertSame(401, $responseInterface->getStatusCode());
    }

    public function testStoreSuccess()
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $request->method('getBody')->willReturn(new class {
            public function getContents()
            {
                return json_encode([
                    'email' => 'test@test.br',
                    'password' => 'xxxx'
                ]);
            }
        });

        $user = new User();
        $user->setId(1);
        $user->setEmail('test@test.br');
        $user->setPassword('xxxx');

        $this->userService->method('getUserModel')
            ->willReturn($user);

        $this->userService->method('store')
            ->willReturn($user);

        $responseInterface = $this->userController->store($request, $response);

        $this->assertSame(201, $responseInterface->getStatusCode());
    }

    public function testStoreFail()
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $response = $this->createMock(ResponseInterface::class);

        $request->method('getBody')->willReturn(new class {
            public function getContents()
            {
                return json_encode([
                    'email' => 'test@test.br',
                    'password' => 'xxxx'
                ]);
            }
        });

        $user = new User();
        $user->setEmail('test@test.br');
        $user->setPassword('xxxx');

        $this->userService->method('getUserModel')
            ->willReturn($user);

        $this->userService->method('store')
            ->willReturn(null);

        $responseInterface = $this->userController->store($request, $response);

        $this->assertSame(500, $responseInterface->getStatusCode());
    }

}