<?php

namespace App\Api\Services;

use App\Models\User;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\ORMException;

class UserService
{
    private EntityRepository $entityRepository;

    public function __construct(private readonly EntityManager $entityManager)
    {
        $this->entityRepository = $this->entityManager->getRepository(User::class);
    }

    public function find(int $id): User
    {
        return $this->entityRepository->findOneBy(compact('id'));
    }

    public function getUserModel(\stdClass $data): User
    {
        $user = new User();
        $user->setEmail($data->email);
        $user->setPassword($data->password);
        $user->setCreated(date(DATE_W3C));

        return $user;
    }

    public function getCredentials(array $user): ?array
    {
        /** @var User $userFetched */
        $userFetched = $this->entityRepository->findOneBy(['email' => $user['email']]);
        $isAuthenticated = password_verify($user['password'], $userFetched->getPassword());

        if (!$isAuthenticated) {
            return null;
        }

        return [
            'token' => $userFetched->generateToken()
        ];
    }

    public function store(User $user): ?User
    {
        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $user;
        } catch (Exception|ORMException $e) {
            return null;
        }
   }
}