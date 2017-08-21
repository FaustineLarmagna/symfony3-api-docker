<?php

namespace ApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class BaseEntity
 */
abstract class BaseEntity
{
    abstract public function getCreatedAt();
    abstract public function setCreatedAt($date);
    abstract public function setUpdatedAt($date);

    /**
     * Add created_at and updated_at before persist if null
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdatedAt(new \DateTime('now'));

        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }
}