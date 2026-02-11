<?php

namespace App\Tests;

use Symfony\Component\HttpFoundation\Response;

class AuthControllerTest extends ApiTestCase
{
    public function testLoginSuccess(): void
    {
        $bodega = $this->createBodega();
        $this->createUser($bodega, 'user@test.com', 'ROLE_ADMIN', 'secret123');

        $client = static::createClient();
        $client->request('POST', '/auth/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'user@test.com',
            'password' => 'secret123'
        ]));

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('accessToken', $data);
    }

    public function testLoginInvalid(): void
    {
        $bodega = $this->createBodega();
        $this->createUser($bodega, 'user2@test.com', 'ROLE_ADMIN', 'secret123');

        $client = static::createClient();
        $client->request('POST', '/auth/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'user2@test.com',
            'password' => 'badpass'
        ]));

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }
}
