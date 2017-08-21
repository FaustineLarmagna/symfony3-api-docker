<?php

namespace ApiBundle\Service;

use ApiBundle\Entity\BaseEntity;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Abstract Class BaseService
 *
 * Handles database queries
 */
abstract class BaseService
{
    private $em;
    private $repository;

    /**
     * @param EntityManager $entityManager
     * @param EntityRepository $repository
     */
    public function __construct(EntityManager $entityManager, EntityRepository $repository)
    {
        $this->em = $entityManager;
        $this->repository = $repository;
    }

    /**
     * Save in database
     *
     * @param BaseEntity $entity
     */
    public function save(BaseEntity $entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    /**
     * Remove entity from database
     *
     * @param BaseEntity $entity
     */
    public function delete(BaseEntity $entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
    }

    /**
     * Retrieve object with id $id
     *
     * @param int $id
     * @return BaseEntity
     * @throws NotFoundHttpException
     */
    public function get(int $id): BaseEntity
    {
        $entity = $this->repository->find($id);

        if (!$entity) {
            throw new NotFoundHttpException(sprintf('Unable to find record with id %d', $id));
        }

        return $entity;
    }

    /**
     * Retrieve all objects
     *
     * @return array
     * @throws NotFoundHttpException
     */
    public function getAll(): array
    {
        $entities = $this->repository->findAll();

        if (!$entities) {
            throw new NotFoundHttpException('Unable to find records');
        }

        return $entities;
    }
}