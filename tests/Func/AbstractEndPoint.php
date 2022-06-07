<?php

namespace App\Tests\Func;

use App\DataFixtures\AppFixtures;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractEndPoint extends WebTestCase
{
    protected array $serverInformations = ['ACCEPT' => 'application/json', 'CONTENT_TYPE' => 'application/json'];
    protected string $admin = '{"username": "admin", "password": "54875487"}';
    protected string $user = '{"username": "user", "password": "54875487"}';
    public function getResponseFromRequest(
        string $method,
        string $uri,
        string $payload = '',
        array $parameter = [],
        bool $withAuthentification = true,
        bool $admin = true
    ): Response {
        $client = $this->createAuthentificationClient($withAuthentification, $admin);
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

    protected function createAuthentificationClient(bool $withAuthentification, bool $admin): KernelBrowser
    {
        self::ensureKernelShutdown();
        $client = static::createClient();

        if (!$withAuthentification) {
            return $client;
        }
        if ($admin){
            $loginPayload = $this->admin;
        }else{
            $loginPayload = $this->user;
        }

        $client->request(
            Request::METHOD_POST,
            '/api/login_check',
            [],
            [],
            $this->serverInformations,
            sprintf(
                $loginPayload,
                AppFixtures::DEFAULT_USER['email'], AppFixtures::DEFAULT_USER['password'], AppFixtures::DEFAULT_USER['first_name'],
                AppFixtures::DEFAULT_USER['last_name'], AppFixtures::DEFAULT_USER['birthday'], AppFixtures::DEFAULT_USER['promo']
            )
        );
        $data = json_decode($client->getResponse()->getContent(), true);
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }
}
