<?php

namespace App\Tests;

use App\Entity\Bodega;
use App\Entity\Stage;
use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class ApiTestCase extends WebTestCase
{
    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $client = static::createClient();
        $this->em = $client->getContainer()->get('doctrine')->getManager();

        $metadata = $this->em->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->em);
        if (!empty($metadata)) {
            $schemaTool->dropSchema($metadata);
            $schemaTool->createSchema($metadata);
        }
    }

    protected function createBodega(string $codigo = 'BODEGA-01'): Bodega
    {
        $bodega = new Bodega();
        $bodega->setCodigo($codigo)->setNombre('Bodega Test')->setPais('ES');
        $this->em->persist($bodega);
        $this->em->flush();
        return $bodega;
    }

    protected function createUser(Bodega $bodega, string $email, string $role = UserRole::ADMIN, string $password = 'pass1234'): User
    {
        $hasher = static::getContainer()->get(UserPasswordHasherInterface::class);
        $user = new User();
        $user->setEmail($email)
            ->setNombre('User')
            ->setRoles([$role])
            ->setBodega($bodega)
            ->setPassword($hasher->hashPassword($user, $password));
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    protected function createStage(Bodega $bodega, string $name = 'Fermentacion', int $order = 1): Stage
    {
        $stage = new Stage();
        $stage->setNombre($name)->setOrden($order)->setBodega($bodega);
        $this->em->persist($stage);
        $this->em->flush();
        return $stage;
    }

    protected function login(string $email, string $password): string
    {
        $client = static::createClient();
        $client->request('POST', '/auth/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => $email,
            'password' => $password
        ]));
        $data = json_decode($client->getResponse()->getContent(), true);
        return $data['accessToken'] ?? '';
    }
}
