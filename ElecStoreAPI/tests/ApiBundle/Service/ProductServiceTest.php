<?php

namespace Tests\ApiBundle\Service;

use ApiBundle\Entity\Product;
use ApiBundle\Repository\ProductRepository;
use ApiBundle\Service\ProductService;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ProductServiceTest
 */
class ProductServiceTest extends WebTestCase
{
    private $product;
    private $productService;

    public function setUp()
    {
        $this->product = new Product();
    }

    /**
     * Initialize ProductService instance with needed mocks
     *
     * @param EntityManager|null $em
     * @param ProductRepository|null $productRepository
     */
    public function init(EntityManager $em = null, ProductRepository $productRepository = null)
    {
        if (!$em) {
            $em = $this->createMock(EntityManager::class);
        }

        if (!$productRepository) {
            $productRepository = $this->createMock(ProductRepository::class);
        }

        $this->productService = new ProductService($em, $productRepository);
    }

    /**
     * Test method save()
     */
    public function testSave()
    {
        $emMock = $this->createMock(EntityManager::class);
        $emMock->expects($this->once())
               ->method('persist')
               ->with($this->product);

        $emMock->expects($this->once())
               ->method('flush');

        $this->init($emMock);

        $this->productService->save($this->product);
    }

    /**
     * Test method delete()
     */
    public function testDelete()
    {
        $emMock = $this->createMock(EntityManager::class);
        $emMock->expects($this->once())
            ->method('remove')
            ->with($this->product);

        $emMock->expects($this->once())
            ->method('flush');

        $this->init($emMock);

        $this->productService->delete($this->product);
    }

    /**
     * Test method get($id)
     */
    public function testGet()
    {
        $id = 1;

        $repositoryMock = $this->createMock(ProductRepository::class);
        $repositoryMock->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn($this->product);

        $this->init(null, $repositoryMock);

        $result = $this->productService->get($id);

        $this->assertInstanceof(Product::class, $result);
    }

    /**
     * Test method getAll()
     */
    public function testGetAll()
    {
        $repositoryMock = $this->createMock(ProductRepository::class);
        $repositoryMock->expects($this->once())
            ->method('findAll')
            ->willReturn([$this->product]);

        $this->init(null, $repositoryMock);

        $result = $this->productService->getAll();

        $this->assertInternalType('array', $result);
        $this->assertInstanceof(Product::class, $result[0]);
    }
}