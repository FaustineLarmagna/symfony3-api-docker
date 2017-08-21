<?php

namespace Tests\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class ControllerTest extends WebTestCase
{
    protected $client;
    protected $em;

    /**
     * Create client with configured hostname
     */
    public function init()
    {
        $this->client = static::createClient();
        $hostname = $this->client->getContainer()->getParameter('hostname');
        $this->client->setServerParameter('HTTP_HOST', $hostname );

        $this->em = $this->client->getContainer()->get('doctrine')->getManager();
    }
}