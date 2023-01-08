<?php

namespace App\Tests\Controller;


use App\Entity\Trick;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TrickControllerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testShowTrick(): void
    {
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->request('GET', '/show/test_1_1');
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1','test TRICK 1');
    }

    public function testDeleteTrick(): void
    {
        $trick = $this->entityManager
            ->getRepository(Trick::class)
            ->findOneBy(['id' => '1'])
        ;
        $this->assertSame('test_1_1', $trick->getslug());
        self::ensureKernelShutdown();
        $client = static::createClient();
        $client->request('GET', '/delete/test_1_1',["page" => "2"]);
        $client->followRedirect();
        $this->assertEquals('',$client->getRequest()->attributes->get('route'));

        //bbe sure trick es removed
        $trick = $this->entityManager
            ->getRepository(Trick::class)
            ->findOneBy(['id' => '1'])
        ;
        $this->assertNull($trick);
    }
}

