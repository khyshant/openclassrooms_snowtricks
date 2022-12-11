<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    public function testSuccessfullLogin(): void
    {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, "/login");

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $form = $crawler->filter('form[name=login_form]')->form([
            "username" => "anthony",
            "password" => "123456",
        ]);
        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();
        $this->assertEquals('',$client->getRequest()->attributes->get('route'));

    }

    public function testfailedLogin(): void
    {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, "/login");

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $form = $crawler->filter('form[name=login_form]')->form([
            "username" => "user1_1",
            "password" => "fail",
        ]);
        $client->submit($form);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $crawler = $client->followRedirect();

        $this->assertEquals('app_login',$client->getRequest()->attributes->get('_route'));
        $this->assertStringContainsString('Invalid credentials.',$crawler->filter('div.alert-danger')->text());

    }
}
