<?php

namespace Tests\ApiBundle\Controller;

use ApiBundle\Entity\Category;
use ApiBundle\Entity\Product;

class ProductControllerTest extends ControllerTest
{
    public function setUp()
    {
        $this->init();
        $this->em->getConnection()->beginTransaction();
    }

    /**
     * Test GET /products/$id with existing id
     */
    public function testGetProductActionSuccess()
    {
        $name = 'MSi Computer';

        $repository = static::$kernel->getContainer()->get('doctrine')->getRepository(Product::class);
        $product = $repository->findOneBy(['name' => $name]);

        $this->assertInstanceOf(Product::class, $product);

        $this->client->request('GET', '/products/'.$product->getId());

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains($name, $this->client->getResponse()->getContent());
    }

    /**
     * Test GET /products/$id with unknown id
     */
    public function testGetProductActionFailure()
    {
        $id = 'abc';

        $this->client->request('GET', '/products/'.$id);

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test POST /products
     */
    public function testPostProductActionSuccess()
    {
        // apiKey loaded in fixtures for post call
        $apiKey = 'testApiKeyUser1';

        // retrieving category id
        $repository = static::$kernel->getContainer()->get('doctrine')->getRepository(Category::class);
        $category = $repository->findOneBy(['name' => 'Computers']);

        $this->assertInstanceOf(Category::class, $category);

        // making the request
        $this->client->request(
            'POST',
            '/products',
            [],
            [],
            ['HTTP_X-AUTH-TOKEN' => $apiKey],
            '{
                "name": "ASUS Computer",
                "category": '.$category->getId().',
                "sku": "C00ASUS",
                "price": 99.99,
                "quantity": 120
            }'
        );

        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Product has been created', $this->client->getResponse()->getContent());
    }

    /**
     * Test POST /products without apiKey
     */
    public function testPostProductActionAuthFailure()
    {
        // making the request
        $this->client->request(
            'POST',
            '/products'
        );

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Authentication Required', $this->client->getResponse()->getContent());
    }

    public function tearDown()
    {
        $this->client = null;
        $this->em->getConnection()->rollback();
    }
}
