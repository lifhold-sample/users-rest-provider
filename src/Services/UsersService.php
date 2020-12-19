<?php

declare(strict_types=1);

namespace Lifhold\Users\Rest\Services;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Lifhold\Users\Contracts\User;
use Lifhold\Users\Exceptions\UserModuleException;
use Lifhold\Users\Exceptions\UserNotFoundException;

class UsersService implements \Lifhold\Users\Contracts\UsersService
{
    protected string $usersApiBaseUrl;


    /**
     * UsersService constructor.
     * @param string $usersApiBaseUrl
     */
    public function __construct(string $usersApiBaseUrl)
    {
        $this->usersApiBaseUrl = $usersApiBaseUrl;
    }

    /**
     * @param string|int $endpoint
     * @return string
     */
    private function endpoint($endpoint): string
    {
        return $this->usersApiBaseUrl . "/" . $endpoint;
    }

    /**
     * @param int|string $endpoint
     * @return User
     * @throws UserModuleException
     * @throws UserNotFoundException
     */
    private function fetchUser($endpoint): User
    {
        $response = Http::get($endpoint);

        if ($response->status() === Response::HTTP_NOT_FOUND) {
            throw new UserNotFoundException("User not found.");
        }

        $validate = \Illuminate\Support\Facades\Validator::make((array)$response, [
            "id" => "required",
            "email" => "required"
        ]);

        if ($validate->fails()) {
            throw new UserModuleException($validate->errors()->toJson());
        }

        return new \Lifhold\Users\Rest\User(
            intval($response['id']),
            strval($response['email'])
        );
    }

    /**
     * @inheritDoc
     */
    public function getOne(int $id): User
    {
        return $this->fetchUser($id);
    }

    /**
     * @inheritDoc
     */
    public function findByEmail(string $email): User
    {
        return $this->fetchUser("findBy/email/{$email}");
    }

    /**
     * @inheritDoc
     */
    public function create(string $email, string $password): User
    {
        $data = [
            "email" => $email,
            "password" => $password
        ];

        $response = Http::post($this->usersApiBaseUrl, $data);

        if ($response->status() !== Response::HTTP_CREATED) {
            throw new UserModuleException("Cant create user with message: {$response->body()}");
        }

        $validate = \Illuminate\Support\Facades\Validator::make((array)$response, [
            "id" => "required",
            "email" => "required"
        ]);

        if ($validate->fails()) {
            throw new UserModuleException($validate->errors()->toJson());
        }

        return new \Lifhold\Users\Rest\User(
            intval($response['id']),
            strval($response['email'])
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(int $id): bool
    {
        $response = Http::delete($this->endpoint($id));

        if ($response->status() === Response::HTTP_OK || $response->status() === Response::HTTP_NO_CONTENT) {
            return boolval(json_decode($response->body()));
        }

        return false;
    }
}
