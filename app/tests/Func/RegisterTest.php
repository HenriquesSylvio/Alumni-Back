<?php

namespace App\Tests\Func;

use App\DataFixtures\UserFixtures;
use Faker\Factory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterTest extends AbstractEndPoint
{
    private string $userPayload = '{"email": "%s", "password": "S-t5S-t5", "first_name": "test", "last_name": "test", "birthday": "25-09-1999", "promo": "25-09-2021"}';

    public function testPostUser(): void
    {
    $response = $this->getResponseFromRequest(
        Request::METHOD_POST,
        '/api/register',
        $this->getPayload(),
        [],
        false
    );
    $responseContent = $response->getContent();
    $responseDecoded = json_decode($responseContent, true);

    //dd($responseDecoded);

    self::assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
    }

    private function getPayload(): string
    {
        $faker = Factory::create();

        return sprintf($this->userPayload, $faker->email);
    }

}