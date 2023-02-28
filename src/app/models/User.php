<?php

namespace App\Models;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Firebase\JWT\JWT;

#[Entity, Table(name: 'users')]
final class User
{
    #[Id, Column(type: 'integer'), GeneratedValue(strategy: 'IDENTITY')]
    protected int $id;

    #[Column(type: 'string', unique: true, nullable: false)]
    protected string $email;

    #[Column(type: 'string', nullable: false)]
    protected string $password;

    #[Column(name: 'created', type: 'string', nullable: false)]
    protected string $created;

    #[Column(name: 'updated', type: 'string', nullable: true)]
    protected string $updated;

    protected ?string $token;

    public function generateToken(): ?string
    {
        $this->token = JWT::encode(['id' => $this->id,
            'email' => $this->email],
            $_ENV['JWT_SECRET']);

        return $this->token;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * @param string $created
     */
    public function setCreated(string $created): void
    {
        $this->created = $created;
    }

    /**
     * @param string $updated
     */
    public function setUpdated(string $updated): void
    {
        $this->updated = $updated;
    }

    /**
     * @param string|null $token
     */
    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }
}