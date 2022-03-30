<?php

namespace App\Tests\Func;

use App\DataFixtures\UserFixtures;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractEndPoint extends WebTestCase
{
    protected array $serverInformations = ['ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'];
    protected string $loginPayload = '{"username": "%s", "password": "%s"}';

    public function getResponseFromRequest(
        string $method,
        string $uri,
        string $payload = '',
        array $parameter = [],
        bool $withAuthentification = true
    ): Response {
        $client = $this->createAuthentificationClient($withAuthentification);

        $client->request(
            $method,
            $uri,
            $parameter,
            [],
            $this->serverInformations,
            $payload
        );

        return $client->getResponse();
    }

    protected function createAuthentificationClient(bool $withAuthentification): KernelBrowser
    {
        $client = static::createClient();

        if (!$withAuthentification) {
            return $client;
        }

        $client->request(
            Request::METHOD_POST,
            '/api/login_check',
            [],
            [],
            $this->serverInformations,
            sprintf(
                $this->loginPayload,
                UserFixtures::DEFAULT_USER['email'], UserFixtures::DEFAULT_USER['password'], UserFixtures::DEFAULT_USER['first_name'],
                UserFixtures::DEFAULT_USER['last_name'], UserFixtures::DEFAULT_USER['birthday'], UserFixtures::DEFAULT_USER['promo']
            )
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }
}
