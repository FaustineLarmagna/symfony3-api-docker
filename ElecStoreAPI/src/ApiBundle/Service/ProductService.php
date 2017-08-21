<?php

namespace ApiBundle\Service;

use ApiBundle\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;

/**
 * Class ProductService
 *
 * Handles database queries for products
 */
class ProductService extends BaseService
{
    /***
     * ProductService constructor.
     *
     * @param EntityManager $entityManager
     * @param ProductRepository $repository
     */
    public function __construct(EntityManager $entityManager, ProductRepository $repository)
    {
        parent::__construct($entityManager, $repository);
    }
}