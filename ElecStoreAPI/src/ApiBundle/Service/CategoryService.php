<?php

namespace ApiBundle\Service;

use ApiBundle\Repository\CategoryRepository;
use Doctrine\ORM\EntityManager;

/**
 * Class CategoryService
 *
 * Handles database queries for categories
 */
class CategoryService extends BaseService
{
    /**
     * CategoryService constructor.
     *
     * @param EntityManager $entityManager
     * @param CategoryRepository $repository
     */
    public function __construct(EntityManager $entityManager, CategoryRepository $repository)
    {
        parent::__construct($entityManager, $repository);
    }
}