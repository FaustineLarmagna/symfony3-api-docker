<?php

namespace ApiBundle\DataFixtures\ORM;

use ApiBundle\Entity\Product;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadProductData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Register Product objects in database
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $product1 = new Product();
        $product1->setName('MSi Computer');
        $product1->setSku("IT001");
        $product1->setCategory($this->getReference('category-Computers'));
        $product1->setQuantity(100);
        $product1->setPrice(999.99);


        $product2 = new Product();
        $product2->setName('Mario Kart Double Dash');
        $product2->setSku("GA001");
        $product2->setCategory($this->getReference('category-Games'));
        $product2->setQuantity(100);
        $product2->setPrice(49.99);


        $manager->persist($product1);
        $manager->persist($product2);
        $manager->flush();
    }

    /**
     * Specify order so this loader is called after CategoryLoader
     * to use category references
     *
     * @return int
     */
    public function getOrder(): int
    {
        return 2;
    }
}