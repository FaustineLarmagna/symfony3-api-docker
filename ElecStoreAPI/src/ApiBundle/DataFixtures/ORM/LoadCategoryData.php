<?php

namespace ApiBundle\DataFixtures\ORM;

use ApiBundle\Entity\Category;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{
    private $categoryNames;

    public function __construct()
    {
        $this->categoryNames = [
            'Computers',
            'Games',
            'Empty'
        ];
    }

    /**
     * Register Category objects in database
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->categoryNames as $name) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);

            $this->addReference('category-'.$name, $category);
        }

        $manager->flush();
    }

    /**
     * Specify order to call this loader before the others
     *
     * @return int
     */
    public function getOrder(): int
    {
        return 1;
    }
}