<?php

namespace ApiBundle\DataFixtures\ORM;

use ApiBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUserData implements FixtureInterface
{
    /**
     * Register User objects in database
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('TestUser1');
        $user->setEmail("user1@test.com");
        $user->setApiKey('testApiKeyUser1');
        $user->setRoles(json_encode(['ROLE_API']));
        
        $manager->persist($user);
        $manager->flush();
    }
}