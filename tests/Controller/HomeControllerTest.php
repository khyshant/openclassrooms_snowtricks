<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class HomeControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', 'http://opc_6_snowtricks.test/');
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1','accroche');
    }
}

