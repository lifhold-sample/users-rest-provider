<?php

declare(strict_types=1);

namespace Lifhold\Users\Rest;

class User implements \Lifhold\Users\Contracts\User
{
    protected int $id;
    protected string $email;

    public function __construct(int $id, string $email)
    {
        $this->id = $id;
        $this->email = $email;
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getEmail(): string
    {
        return $this->email;
    }
}
