<?php
namespace TurboLabIt\TLIBaseBundle\Test;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


abstract class TLIWebTestBase extends WebTestCase
{
    protected $testedServiceName = null;


    protected function getService(string $serviceName)
    {
        return static::getContainer()->get($serviceName);
    }


    protected function getInstance()
    {
        return $this->getService($this->testedServiceName);
    }


    protected function getEntityManager()
    {
        return $this->getService("doctrine")->getManager();
    }


    protected function getRepository(string $repoName)
    {
        return $this->getService("doctrine")->getManager()->getRepository($repoName);
    }
}
