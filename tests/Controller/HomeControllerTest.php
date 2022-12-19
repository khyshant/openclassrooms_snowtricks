<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class HomeControllerTest extends WebTestCase
{
    public function testHome(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorTextContains('h1','accroche');
    }


    public function testMoreTricks(): void
    {
        $client = static::createClient();
        $client->request('GET', '/moretricks',["page" => "2"]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorExists('#forTricks_2');
    }
}

