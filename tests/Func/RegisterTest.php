<?php

namespace App\Tests\Func;

use App\DataFixtures\AppFixtures;
use Faker\Factory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterTest extends AbstractEndPoint
{
    private string $userPayload = '{"email": "%s", "password": "S-t5S-t5", "username": "%s", "first_name": "test", "last_name": "test", "promo": "2017"}';

    public function testcreateUser(): void
    {
    $response = $this->getResponseFromRequest(
        Request::METHOD_POST,
        '/api/register',
        $this->getPayload(),
        [],
        false
    );
    self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    private function getPayload(): string
    {
        $faker = Factory::create();
        return sprintf($this->userPayload, $faker->email, $faker->userName);
    }

}